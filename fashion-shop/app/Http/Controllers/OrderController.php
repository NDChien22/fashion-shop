<?php

namespace App\Http\Controllers;

use App\Enums\OrderReturnRequestStatus;
use App\Enums\OrderReturnRequestType;
use App\Enums\OrderStatus;
use App\Enums\ShippingStatus;
use App\Mail\OrderStatusNotificationEmail;
use App\Models\Order;
use App\Models\OrderFeedback;
use App\Models\OrderReturnRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private const RETURN_WINDOW_DAYS = 7;

    public function index(Request $request)
    {
        $searchKeyword = trim((string) $request->query('q', ''));

        $query = Order::query()
            ->with([
                'items.productSku.product:id,name,main_image_url',
                'payment:id,order_id,status,payment_method,transaction_id,amount',
                'feedback:id,order_id,product_id,user_id,rating,content,created_at',
                'returnRequest:id,order_id,user_id,request_type,status,reason,details,evidence_images,admin_note,admin_id,resolved_at,created_at',
            ])
            ->orderByDesc('id');

        if ($request->user()) {
            $query->where('user_id', auth()->id());
        } else {
            $guestOrderCodes = collect($request->session()->get('guest_order_codes', []))
                ->filter(fn ($code) => is_string($code) && trim($code) !== '')
                ->values()
                ->all();

            if ($searchKeyword === '' && empty($guestOrderCodes)) {
                $query->whereRaw('1 = 0');
                $orders = $query->paginate(5)->withQueryString();

                return view('pages.user.order.index', [
                    'orders' => $orders,
                    'isGuest' => true,
                    'searchKeyword' => $searchKeyword,
                ]);
            }

            $query->where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', 0);
            });

            if ($searchKeyword === '' && ! empty($guestOrderCodes)) {
                $query->whereIn('order_code', $guestOrderCodes);
            }
        }

        if ($searchKeyword !== '') {
            $normalizedOrderCode = strtoupper($searchKeyword);

            if ($request->user()) {
                // For logged in users we only search by order code to avoid referencing missing guest columns
                $query->where(function (Builder $scope) use ($normalizedOrderCode): void {
                    $scope->orWhereRaw('UPPER(order_code) LIKE ?', ['%'.$normalizedOrderCode.'%']);
                });
            } else {
                // For guest search, match against guest_* columns which exist in the schema
                $query->where(function (Builder $scope) use ($searchKeyword, $normalizedOrderCode): void {
                    $scope->where('guest_email', 'like', '%'.$searchKeyword.'%')
                        ->orWhere('guest_phone', 'like', '%'.$searchKeyword.'%')
                        ->orWhereRaw('UPPER(order_code) LIKE ?', ['%'.$normalizedOrderCode.'%']);
                });
            }
        }

        $orders = $query->paginate(5)->withQueryString();

        return view('pages.user.order.index', [
            'orders' => $orders,
            'isGuest' => ! $request->user(),
            'searchKeyword' => $searchKeyword,
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if (! $this->canAccessOrder($request, $order)) {
            abort(403);
        }

        if ((string) $order->status === OrderStatus::CANCELLED->value) {
            return back()->with('error', 'Đơn hàng đã được hủy trước đó.');
        }

        if ((string) $order->shipping_status === ShippingStatus::DELIVERED->value) {
            return back()->with('error', 'Chỉ có thể hủy đơn chưa giao.');
        }

        $order->update([
            'status' => OrderStatus::CANCELLED->value,
            'shipping_status' => ShippingStatus::CANCELLED->value,
        ]);

        if (is_string($order->customer_email) && trim($order->customer_email) !== '') {
            try {
                Mail::to($order->customer_email)->send(
                    new OrderStatusNotificationEmail($order, OrderStatusNotificationEmail::EVENT_CANCELLED)
                );
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return back()->with('success', 'Đã hủy đơn hàng thành công.');
    }

    public function storeFeedback(Request $request, Order $order): RedirectResponse
    {
        if (! $this->canAccessOrder($request, $order)) {
            abort(403);
        }

        if (
            (string) $order->status !== OrderStatus::COMPLETED->value
            && (string) $order->shipping_status !== ShippingStatus::DELIVERED->value
        ) {
            return back()->with('error', 'Chỉ có thể gửi feedback cho đơn hàng đã hoàn thành.');
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $productId = (int) $validated['product_id'];

        $existsInOrder = $order->items->contains(function ($item) use ($productId) {
            return (int) $item->productSku?->product_id === $productId;
        });

        if (! $existsInOrder) {
            return back()->with('error', 'Sản phẩm không thuộc đơn hàng.');
        }

        $feedbackExists = OrderFeedback::query()
            ->where('order_id', $order->id)
            ->where('product_id', $productId)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($feedbackExists) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này.');
        }

        OrderFeedback::query()->create([
            'order_id' => $order->id,
            'product_id' => $productId,
            'user_id' => $request->user()?->id,
            'rating' => $validated['rating'],
            'content' => trim($validated['content']),
        ]);

        return back()->with('success', 'Đã gửi đánh giá thành công.');
    }

    public function storeReturnRequest(Request $request, Order $order): RedirectResponse
    {
        if (! $this->canAccessOrder($request, $order)) {
            abort(403);
        }

        if (! $this->canRequestReturn($order)) {
            return back()->with('error', 'Chỉ có thể gửi yêu cầu đổi/trả cho đơn hàng đã giao hoặc đã hoàn thành.');
        }

        if (! $this->isWithinReturnWindow($order)) {
            return back()->with('error', 'Yêu cầu đổi/trả chỉ được thực hiện trong vòng 7 ngày kể từ ngày hoàn thành đơn hàng.');
        }

        $existingRequest = $order->returnRequest;
        $existingRequestStatus = $existingRequest?->status?->value ?? (string) $existingRequest?->status;

        if ($existingRequest && $existingRequestStatus === OrderReturnRequestStatus::COMPLETED->value) {
            return back()->with('error', 'Yêu cầu đổi/trả đã hoàn tất, bạn không thể tạo lại cho đơn này.');
        }

        if ($existingRequest && in_array($existingRequestStatus, [
            OrderReturnRequestStatus::PENDING->value,
            OrderReturnRequestStatus::APPROVED->value,
        ], true)) {
            return back()->with('error', 'Đơn hàng này đang có yêu cầu đổi/trả đang được xử lý.');
        }

        $validated = $request->validate([
            'return_order_id' => ['required', 'integer'],
            'request_type' => ['required', Rule::in(OrderReturnRequestType::values())],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
            'details' => ['nullable', 'string', 'max:5000'],
            'evidence_images' => ['nullable', 'array', 'max:5'],
            'evidence_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'request_type.required' => 'Vui lòng chọn loại yêu cầu.',
            'request_type.in' => 'Loại yêu cầu không hợp lệ.',
            'reason.required' => 'Vui lòng nhập lý do đổi/trả.',
            'reason.min' => 'Lý do đổi/trả phải có ít nhất 10 ký tự.',
            'reason.max' => 'Lý do đổi/trả không được vượt quá 500 ký tự.',
            'details.max' => 'Mô tả thêm không được vượt quá 5000 ký tự.',
            'evidence_images.array' => 'Ảnh minh chứng không hợp lệ.',
            'evidence_images.max' => 'Bạn chỉ có thể gửi tối đa 5 ảnh minh chứng.',
            'evidence_images.*.image' => 'Tệp tải lên phải là ảnh.',
            'evidence_images.*.mimes' => 'Ảnh chỉ hỗ trợ jpg, jpeg, png, webp.',
            'evidence_images.*.max' => 'Mỗi ảnh minh chứng không được vượt quá 2MB.',
        ]);

        if ((int) $validated['return_order_id'] !== (int) $order->id) {
            return back()->with('error', 'Dữ liệu yêu cầu đổi/trả không hợp lệ.');
        }

        $payload = [
            'order_id' => (int) $order->id,
            'user_id' => $request->user()?->id,
            'request_type' => $validated['request_type'],
            'reason' => trim((string) $validated['reason']),
            'details' => isset($validated['details']) ? trim((string) $validated['details']) : null,
            'evidence_images' => $this->storeReturnEvidenceImages($request, $order, $existingRequest),
            'status' => OrderReturnRequestStatus::PENDING->value,
            'admin_note' => null,
            'admin_id' => null,
            'resolved_at' => null,
        ];

        if ($existingRequest) {
            $existingRequest->update($payload);
        } else {
            OrderReturnRequest::query()->create($payload);
        }

        return back()->with('success', 'Đã gửi yêu cầu đổi/trả. Bộ phận chăm sóc khách hàng sẽ liên hệ sớm.');
    }

    /**
     * @return array<int, string>
     */
    private function storeReturnEvidenceImages(Request $request, Order $order, ?OrderReturnRequest $existingRequest = null): array
    {
        if (! $request->hasFile('evidence_images')) {
            return $existingRequest?->evidence_images ?? [];
        }

        // Delete previous files (if any)
        foreach (($existingRequest?->evidence_images ?? []) as $existingImage) {
            if (! is_string($existingImage) || $existingImage === '') {
                continue;
            }

            $this->deletePublicStorageFile($existingImage);
        }

        $storedImages = [];
        foreach ($request->file('evidence_images', []) as $imageFile) {
            if (! $imageFile) {
                continue;
            }

            $path = $imageFile->store('return-requests', 'public');
            // follow product upload convention: store public URL path (storage/...)
            $storedImages[] = 'storage/'.$path;
        }

        return $storedImages;
    }

    private function deletePublicStorageFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', (string) $path), '/');
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if ($normalizedPath !== '' && Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }
    }

    private function canAccessOrder(Request $request, Order $order): bool
    {
        if ($request->user()) {
            return (int) $order->user_id === (int) $request->user()->id;
        }

        return in_array(
            strtoupper((string) $order->order_code),
            $this->guestOrderCodes($request),
            true,
        );
    }

    private function guestOrderCodes(Request $request): array
    {
        return collect($request->session()->get('guest_order_codes', []))
            ->filter(fn ($code) => is_string($code) && trim($code) !== '')
            ->map(fn ($code) => strtoupper(trim((string) $code)))
            ->all();
    }

    private function canRequestReturn(Order $order): bool
    {
        return (string) $order->shipping_status === ShippingStatus::DELIVERED->value
            || (string) $order->status === OrderStatus::COMPLETED->value;
    }

    private function isWithinReturnWindow(Order $order): bool
    {
        $completedAt = $this->resolveReturnWindowStart($order);
        if (! $completedAt) {
            return false;
        }

        return now()->lte($completedAt->copy()->addDays(self::RETURN_WINDOW_DAYS));
    }

    private function resolveReturnWindowStart(Order $order): ?Carbon
    {
        if ((string) $order->status === OrderStatus::COMPLETED->value) {
            return $order->updated_at;
        }

        if ((string) $order->shipping_status === ShippingStatus::DELIVERED->value) {
            return $order->updated_at;
        }

        return null;
    }
}
