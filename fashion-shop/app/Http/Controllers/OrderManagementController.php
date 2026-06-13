<?php

namespace App\Http\Controllers;

use App\Enums\OrderReturnRequestStatus;
use App\Enums\OrderReturnRequestType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Mail\OrderStatusNotificationEmail;
use App\Models\CustomerMembershipLevel;
use App\Models\MembershipLevel;
use App\Models\Order;
use App\Models\OrderReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderManagementController extends Controller
{
    private const POINTS_PER_AMOUNT = 10000;

    public function index(Request $request)
    {
        $this->syncCompletedOrdersStatuses();

        $query = Order::query()
            ->with([
                'payment:id,order_id,status,payment_method,transaction_id,amount',
                'items:id,order_id,product_sku_id,product_name,product_sku,product_size,product_color,quantity,price',
                'items.productSku:id,product_id,sku,size,color',
                'items.productSku.product:id,name',
                'returnRequest:id,order_id,user_id,request_type,status,reason,details,evidence_images,admin_note,admin_id,resolved_at,created_at',
            ]);

        $keyword = trim((string) $request->query('q', ''));
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('order_code', 'like', '%'.$keyword.'%')
                    ->orWhere('guest_name', 'like', '%'.$keyword.'%')
                    ->orWhere('guest_phone', 'like', '%'.$keyword.'%');
            });
        }

        $status = (string) $request->query('status', '');
        if (in_array($status, OrderStatus::values(), true)) {
            $query->where('status', $status);
        }

        $shippingStatus = (string) $request->query('shipping_status', '');
        if (in_array($shippingStatus, ShippingStatus::values(), true)) {
            $query->where('shipping_status', $shippingStatus);
        }

        $paymentStatus = (string) $request->query('payment_status', '');
        if (in_array($paymentStatus, PaymentStatus::values(), true)) {
            $query->whereHas('payment', function ($builder) use ($paymentStatus): void {
                $builder->where('status', $paymentStatus);
            });
        }

        $returnStatus = (string) $request->query('return_status', '');
        if ($returnStatus !== '') {
            if ($returnStatus === 'has_return') {
                $query->whereHas('returnRequest');
            } elseif (in_array($returnStatus, OrderReturnRequestStatus::values(), true)) {
                $query->whereHas('returnRequest', function ($builder) use ($returnStatus): void {
                    $builder->where('status', $returnStatus);
                });
            }
        }

        $orders = $query
            ->orderByRaw(
                'CASE WHEN status IN (?, ?, ?) THEN 0 ELSE 1 END, id DESC',
                [
                    OrderStatus::PENDING->value,
                    OrderStatus::PROCESSING->value,
                    OrderStatus::PAYMENT_FAILED->value,
                ]
            )
            ->paginate(12)
            ->withQueryString();

        $summary = [
            'total' => (int) Order::query()->count(),
            'pending' => (int) Order::query()->where('status', OrderStatus::PENDING->value)->count(),
            'processing' => (int) Order::query()->where('status', OrderStatus::PROCESSING->value)->count(),
            'completed' => (int) Order::query()->where('status', OrderStatus::COMPLETED->value)->count(),
            'returned' => (int) Order::query()->where('status', OrderStatus::RETURNED->value)->count(),
            'exchanged' => (int) Order::query()->where('status', OrderStatus::EXCHANGED->value)->count(),
            'payment_failed' => (int) Order::query()->where('status', OrderStatus::PAYMENT_FAILED->value)->count(),
            'return_total' => (int) OrderReturnRequest::query()->count(),
            'return_pending' => (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::PENDING->value)->count(),
            'return_approved' => (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::APPROVED->value)->count(),
            'return_rejected' => (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::REJECTED->value)->count(),
            'return_completed' => (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::COMPLETED->value)->count(),
            'return_returned' => (int) Order::query()->where('status', OrderStatus::RETURNED->value)->count(),
            'return_exchanged' => (int) Order::query()->where('status', OrderStatus::EXCHANGED->value)->count(),
        ];

        return view('pages.admin.order-manager.order-manager', [
            'orders' => $orders,
            'summary' => $summary,
            'orderStatuses' => OrderStatus::cases(),
            'shippingStatuses' => ShippingStatus::cases(),
            'paymentStatuses' => PaymentStatus::cases(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        if ((string) $order->status === OrderStatus::COMPLETED->value) {
            $this->syncCompletedOrdersStatuses((int) $order->id);

            return back()->with('error', 'Đơn đã hoàn thành, không thể chỉnh sửa thêm.');
        }

        if ((string) $order->status === OrderStatus::CANCELLED->value) {
            return back()->with('error', 'Đơn đã hủy, không thể chỉnh sửa thêm.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', OrderStatus::values())],
            'shipping_status' => ['required', 'in:'.implode(',', ShippingStatus::values())],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'shipping_status.required' => 'Vui lòng chọn trạng thái vận chuyển.',
        ]);

        $rewardContext = [
            'points' => 0,
            'level_name' => null,
        ];

        $previousOrderStatus = (string) $order->status;
        $previousShippingStatus = (string) $order->shipping_status;

        $nextOrderStatus = (string) $validated['status'];
        $nextShippingStatus = (string) $validated['shipping_status'];

        // Once order is completed, synchronize related statuses to final state.
        if ($nextOrderStatus === OrderStatus::COMPLETED->value) {
            $nextShippingStatus = ShippingStatus::DELIVERED->value;
        }

        DB::transaction(function () use ($request, $order, $nextOrderStatus, $nextShippingStatus, &$rewardContext): void {
            $previousShippingStatus = (string) $order->shipping_status;
            $employeeId = $request->user()?->employee?->id;

            $order->update([
                'status' => $nextOrderStatus,
                'shipping_status' => $nextShippingStatus,
                'staff_id' => $employeeId,
            ]);

            $this->syncOrderPaymentStatus($order);

            $rewardContext = $this->applyRewardOnDelivered($order, $previousShippingStatus, $nextShippingStatus);
        });

        $order->refresh();
        $this->sendStatusEmailsAfterUpdate($order, $previousOrderStatus, $previousShippingStatus);

        $successMessage = 'Đã cập nhật trạng thái đơn hàng.';
        if (($rewardContext['points'] ?? 0) > 0) {
            $successMessage .= ' Đã cộng '.number_format((int) $rewardContext['points']).' điểm tích lũy';

            if (! empty($rewardContext['level_name'])) {
                $successMessage .= ' - hạng hiện tại: '.$rewardContext['level_name'];
            }

            $successMessage .= '.';
        }

        return back()->with('success', $successMessage);
    }

    public function updateReturnRequest(Request $request, OrderReturnRequest $returnRequest): RedirectResponse
    {
        $returnRequest->loadMissing('order:id,status,shipping_status,payment_method');

        if ((string) ($returnRequest->status?->value ?? $returnRequest->status) === OrderReturnRequestStatus::COMPLETED->value) {
            return back()->with('error', 'Yêu cầu đổi/trả đã hoàn tất, không thể cập nhật thêm.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', OrderReturnRequestStatus::values())],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái xử lý.',
            'status.in' => 'Trạng thái xử lý không hợp lệ.',
            'admin_note.max' => 'Ghi chú quản trị không được vượt quá 2000 ký tự.',
        ]);

        $nextStatus = OrderReturnRequestStatus::tryFrom((string) $validated['status']);
        if (! $nextStatus) {
            return back()->with('error', 'Trạng thái xử lý không hợp lệ.');
        }

        DB::transaction(function () use ($request, $returnRequest, $validated, $nextStatus): void {
            $employeeId = $request->user()?->employee?->id;
            $resolvedAt = in_array($nextStatus, [
                OrderReturnRequestStatus::REJECTED,
                OrderReturnRequestStatus::COMPLETED,
            ], true) ? now() : null;

            $returnRequest->update([
                'status' => $nextStatus->value,
                'admin_note' => isset($validated['admin_note']) ? trim((string) $validated['admin_note']) : null,
                'admin_id' => $employeeId,
                'resolved_at' => $resolvedAt,
            ]);

            $returnRequest->loadMissing('order.payment', 'order.returnRequest');

            if ($nextStatus === OrderReturnRequestStatus::COMPLETED) {
                $returnRequest->order?->update([
                    'status' => $this->resolveReturnCompletionOrderStatus($returnRequest),
                ]);
            }

            $this->syncOrderPaymentStatus($returnRequest->order);
        });

        return back()->with('success', 'Đã cập nhật trạng thái yêu cầu đổi/trả.');
    }

    private function sendStatusEmailsAfterUpdate(Order $order, string $previousOrderStatus, string $previousShippingStatus): void
    {
        if (! is_string($order->customer_email) || trim($order->customer_email) === '') {
            return;
        }

        try {
            if ($previousShippingStatus !== ShippingStatus::DELIVERED->value
                && (string) $order->shipping_status === ShippingStatus::DELIVERED->value) {
                Mail::to($order->customer_email)->send(
                    new OrderStatusNotificationEmail($order, OrderStatusNotificationEmail::EVENT_DELIVERED)
                );
            }

            if ($previousOrderStatus !== OrderStatus::COMPLETED->value
                && (string) $order->status === OrderStatus::COMPLETED->value) {
                Mail::to($order->customer_email)->send(
                    new OrderStatusNotificationEmail($order, OrderStatusNotificationEmail::EVENT_COMPLETED)
                );
            }

            if ($previousOrderStatus !== OrderStatus::CANCELLED->value
                && (string) $order->status === OrderStatus::CANCELLED->value) {
                Mail::to($order->customer_email)->send(
                    new OrderStatusNotificationEmail($order, OrderStatusNotificationEmail::EVENT_CANCELLED)
                );
            }
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function syncCompletedOrdersStatuses(?int $orderId = null): void
    {
        $ordersQuery = Order::query()
            ->with(['payment:id,order_id,status,payment_method,transaction_id,amount', 'returnRequest:id,order_id,request_type,status'])
            ->where('status', OrderStatus::COMPLETED->value);
        if (! is_null($orderId)) {
            $ordersQuery->whereKey($orderId);
        }

        $ordersQuery->get()->each(function (Order $completedOrder): void {
            if ((string) $completedOrder->shipping_status !== ShippingStatus::DELIVERED->value) {
                $completedOrder->update([
                    'shipping_status' => ShippingStatus::DELIVERED->value,
                ]);
            }

            $this->syncOrderPaymentStatus($completedOrder);
        });
    }

    private function syncOrderPaymentStatus(Order $order): void
    {
        $order->loadMissing(['payment', 'returnRequest']);

        if (! $order->payment) {
            return;
        }

        $nextPaymentStatus = null;

        if (in_array((string) $order->status, [
            OrderStatus::COMPLETED->value,
            OrderStatus::RETURNED->value,
            OrderStatus::EXCHANGED->value,
        ], true)) {
            $returnRequestType = $order->returnRequest?->request_type;
            $returnRequestStatus = $order->returnRequest?->status;

            $isCompletedReturnRequest = $order->returnRequest
                && ($returnRequestType instanceof OrderReturnRequestType
                    ? $returnRequestType->value
                    : (string) $returnRequestType) === OrderReturnRequestType::RETURN->value
                && ($returnRequestStatus instanceof OrderReturnRequestStatus
                    ? $returnRequestStatus->value
                    : (string) $returnRequestStatus) === OrderReturnRequestStatus::COMPLETED->value;

            if ($isCompletedReturnRequest) {
                $nextPaymentStatus = PaymentStatus::REFUNDED->value;
            } elseif ((string) $order->status === OrderStatus::EXCHANGED->value) {
                $nextPaymentStatus = PaymentStatus::PAID->value;
            } else {
                $nextPaymentStatus = PaymentStatus::PAID->value;
            }
        }

        if (is_null($nextPaymentStatus) || (string) $order->payment->status === $nextPaymentStatus) {
            return;
        }

        $order->payment->update([
            'status' => $nextPaymentStatus,
        ]);
    }

    private function applyRewardOnDelivered(Order $order, string $previousShippingStatus, string $nextShippingStatus): array
    {
        if ($previousShippingStatus === ShippingStatus::DELIVERED->value || $nextShippingStatus !== ShippingStatus::DELIVERED->value) {
            return ['points' => 0, 'level_name' => null];
        }

        if ((int) $order->user_id <= 0) {
            return ['points' => 0, 'level_name' => null];
        }

        $basePoints = (int) floor(((float) $order->final_amount) / self::POINTS_PER_AMOUNT);
        if ($basePoints <= 0) {
            return ['points' => 0, 'level_name' => null];
        }

        $this->ensureMembershipLevels();

        $customerMembership = CustomerMembershipLevel::query()->firstOrCreate(
            ['user_id' => (int) $order->user_id],
            [
                'customer_code' => $this->generateUniqueCustomerCode(),
                'membership_level_id' => $this->defaultMembershipLevelId(),
                'points' => 0,
            ]
        );

        $currentLevel = MembershipLevel::query()->find((int) $customerMembership->membership_level_id);
        $pointConversionRate = max(0, (int) ($currentLevel?->point_conversion_rate ?? 0));
        $earnedPoints = (int) round($basePoints * (1 + ($pointConversionRate / 100)), 0);

        $currentPoints = (int) $customerMembership->points;
        $updatedPoints = $currentPoints + $earnedPoints;

        $targetLevel = MembershipLevel::query()
            ->where('min_points', '<=', $updatedPoints)
            ->orderByDesc('min_points')
            ->first();

        $customerMembership->update([
            'points' => $updatedPoints,
            'membership_level_id' => (int) ($targetLevel?->id ?? $customerMembership->membership_level_id),
        ]);

        return [
            'points' => $earnedPoints,
            'level_name' => $targetLevel?->name,
        ];
    }

    private function ensureMembershipLevels(): void
    {
        $tiers = [
            ['name' => 'Thành viên mới', 'min_points' => 0, 'point_conversion_rate' => 0, 'discount_rate' => 0],
            ['name' => 'Bạc', 'min_points' => 500, 'point_conversion_rate' => 2, 'discount_rate' => 2],
            ['name' => 'Vàng', 'min_points' => 1500, 'point_conversion_rate' => 5, 'discount_rate' => 5],
            ['name' => 'Bạch kim', 'min_points' => 3000, 'point_conversion_rate' => 8, 'discount_rate' => 8],
            ['name' => 'Kim cương', 'min_points' => 6000, 'point_conversion_rate' => 10, 'discount_rate' => 10],
        ];

        foreach ($tiers as $tier) {
            MembershipLevel::query()->updateOrCreate(
                ['name' => $tier['name']],
                [
                    'min_points' => $tier['min_points'],
                    'point_conversion_rate' => $tier['point_conversion_rate'],
                    'discount_rate' => $tier['discount_rate'],
                ]
            );
        }
    }

    private function defaultMembershipLevelId(): int
    {
        $defaultLevel = MembershipLevel::query()->firstOrCreate(
            ['name' => 'Thành viên mới'],
            [
                'min_points' => 0,
                'point_conversion_rate' => 0,
                'discount_rate' => 0,
            ]
        );

        return (int) $defaultLevel->id;
    }

    private function generateUniqueCustomerCode(): string
    {
        do {
            $customerCode = 'KH'.now()->format('ymd').strtoupper(Str::random(4));
        } while (CustomerMembershipLevel::query()->where('customer_code', $customerCode)->exists());

        return $customerCode;
    }

    private function resolveReturnCompletionOrderStatus(OrderReturnRequest $returnRequest): string
    {
        $returnType = $returnRequest->request_type;

        if ($returnType instanceof OrderReturnRequestType) {
            return $returnType === OrderReturnRequestType::RETURN
                ? OrderStatus::RETURNED->value
                : OrderStatus::EXCHANGED->value;
        }

        return (string) $returnType === OrderReturnRequestType::RETURN->value
            ? OrderStatus::RETURNED->value
            : OrderStatus::EXCHANGED->value;
    }

    private function isWithinReturnWindow(Order $order): bool
    {
        $completedAt = (string) $order->status === OrderStatus::COMPLETED->value
            || (string) $order->shipping_status === ShippingStatus::DELIVERED->value
            ? $order->updated_at
            : null;

        if (! $completedAt) {
            return false;
        }

        return now()->lte($completedAt->copy()->addDays(7));
    }
}
