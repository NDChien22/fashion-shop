<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\ShippingStatus;
use App\Mail\OrderStatusNotificationEmail;
use App\Models\Order;
use App\Models\OrderFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $searchKeyword = trim((string) $request->query('q', ''));

        $query = Order::query()
            ->with([
                'items.productSku.product:id,name,main_image_url',
                'payment:id,order_id,status,payment_method,transaction_id,amount',
                'feedback:id,order_id,product_id,user_id,rating,content,created_at',
            ])
            ->orderByDesc('id');

        if ($request->user()) {
            $query->where('user_id', (int) $request->user()->id);
        } else {
            $guestOrderCodes = collect($request->session()->get('guest_order_codes', []))
                ->filter(fn ($code) => is_string($code) && trim($code) !== '')
                ->values()
                ->all();

            if ($searchKeyword === '' && empty($guestOrderCodes)) {
                $orders = collect();

                return view('pages.user.order.index', [
                    'orders' => $orders,
                    'isGuest' => true,
                    'searchKeyword' => $searchKeyword,
                ]);
            }

            $query->where('user_id', 0);

            if ($searchKeyword === '' && ! empty($guestOrderCodes)) {
                $query->whereIn('order_code', $guestOrderCodes);
            }
        }

        if ($searchKeyword !== '') {
            $normalizedOrderCode = strtoupper($searchKeyword);

            $query->where(function (Builder $scope) use ($searchKeyword, $normalizedOrderCode): void {
                $scope->where('customer_email', 'like', '%'.$searchKeyword.'%')
                    ->orWhere('customer_phone', 'like', '%'.$searchKeyword.'%')
                    ->orWhereRaw('UPPER(order_code) LIKE ?', ['%'.$normalizedOrderCode.'%']);
            });
        }

        $orders = $query->get();

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

        if ($order->feedback) {
            return back()->with('error', 'Đơn hàng này đã có feedback trước đó.');
        }

        $productId = (int) ($order->items->first()?->productSku?->product_id ?? 0);
        if ($productId <= 0) {
            return back()->with('error', 'Không xác định được sản phẩm để lưu feedback.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.integer' => 'Số sao không hợp lệ.',
            'rating.between' => 'Số sao phải từ 1 đến 5.',
            'content.required' => 'Vui lòng nhập nội dung feedback.',
            'content.min' => 'Nội dung feedback phải có ít nhất 10 ký tự.',
            'content.max' => 'Nội dung feedback không được vượt quá 2000 ký tự.',
        ]);

        OrderFeedback::query()->create([
            'order_id' => (int) $order->id,
            'product_id' => $productId,
            'user_id' => $request->user()?->id,
            'rating' => (int) $validated['rating'],
            'content' => trim((string) $validated['content']),
        ]);

        return back()->with('success', 'Đã gửi feedback cho đơn hàng. Cảm ơn bạn đã chia sẻ trải nghiệm.');
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
}
