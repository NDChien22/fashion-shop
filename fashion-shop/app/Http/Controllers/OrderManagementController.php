<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Mail\OrderStatusNotificationEmail;
use App\Models\CustomerMembershipLevel;
use App\Models\MembershipLevel;
use App\Models\Order;
use App\Models\Payment;
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
                'items:id,order_id,product_sku_id,quantity,price',
                'items.productSku:id,product_id,sku,size,color',
                'items.productSku.product:id,name',
            ]);

        $keyword = trim((string) $request->query('q', ''));
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('order_code', 'like', '%'.$keyword.'%')
                    ->orWhere('customer_name', 'like', '%'.$keyword.'%')
                    ->orWhere('customer_phone', 'like', '%'.$keyword.'%');
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
            'payment_failed' => (int) Order::query()->where('status', OrderStatus::PAYMENT_FAILED->value)->count(),
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
            'payment_status' => ['nullable', 'in:'.implode(',', PaymentStatus::values())],
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
        $nextPaymentStatus = array_key_exists('payment_status', $validated)
            ? (string) $validated['payment_status']
            : null;

        // Once order is completed, synchronize related statuses to final state.
        if ($nextOrderStatus === OrderStatus::COMPLETED->value) {
            $nextShippingStatus = ShippingStatus::DELIVERED->value;
            $nextPaymentStatus = PaymentStatus::PAID->value;
        }

        DB::transaction(function () use ($order, $nextOrderStatus, $nextShippingStatus, $nextPaymentStatus, &$rewardContext): void {
            $previousShippingStatus = (string) $order->shipping_status;

            $order->update([
                'status' => $nextOrderStatus,
                'shipping_status' => $nextShippingStatus,
            ]);

            if (! is_null($nextPaymentStatus) && $order->payment) {
                $order->payment->update([
                    'status' => $nextPaymentStatus,
                ]);
            }

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
        $ordersQuery = Order::query()->where('status', OrderStatus::COMPLETED->value);
        if (! is_null($orderId)) {
            $ordersQuery->whereKey($orderId);
        }

        $ordersQuery
            ->where('shipping_status', '!=', ShippingStatus::DELIVERED->value)
            ->update([
                'shipping_status' => ShippingStatus::DELIVERED->value,
            ]);

        $paymentsQuery = Payment::query()
            ->whereHas('order', function ($builder): void {
                $builder->where('status', OrderStatus::COMPLETED->value);
            });

        if (! is_null($orderId)) {
            $paymentsQuery->where('order_id', $orderId);
        }

        $paymentsQuery
            ->where('status', '!=', PaymentStatus::PAID->value)
            ->update([
                'status' => PaymentStatus::PAID->value,
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
}
