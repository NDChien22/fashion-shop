<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Enums\VoucherStatus;
use App\Mail\OrderStatusNotificationEmail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderVoucher;
use App\Models\Payment;
use App\Models\Products;
use App\Models\ProductSkus;
use App\Models\UserVoucher;
use App\Models\Voucher;
use App\Support\FlashSalePricing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Throwable;

class CheckoutController extends Controller
{
    private const SHIPPING_FEE = 30000;

    private const FREE_SHIPPING_THRESHOLD = 499000;

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'selected_cart_ids' => ['required', 'array', 'min:1'],
            'selected_cart_ids.*' => ['integer'],
            'voucher_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'in:cod,vnpay,stripe'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
        ], [
            'selected_cart_ids.required' => 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.',
            'selected_cart_ids.min' => 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        $user = $request->user();

        if ($user) {
            $resolvedPhone = trim((string) ($validated['customer_phone'] ?? $user->phone_number ?? ''));
            $resolvedAddress = trim((string) ($validated['shipping_address'] ?? $user->address ?? ''));

            if ($resolvedPhone === '' || $resolvedAddress === '') {
                return back()->with('error', 'Vui lòng cập nhật đầy đủ số điện thoại và địa chỉ trong hồ sơ trước khi đặt hàng.');
            }

            $validated['customer_phone'] = $resolvedPhone;
            $validated['shipping_address'] = $resolvedAddress;
        }

        if (! $user) {
            $guestValidated = $request->validate([
                'customer_name' => ['required', 'string', 'max:255'],
                'customer_phone' => ['required', 'string', 'max:20'],
                'shipping_address' => ['required', 'string', 'max:500'],
            ], [
                'customer_name.required' => 'Vui lòng nhập họ tên người nhận.',
                'customer_phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
                'shipping_address.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
            ]);

            $validated = array_merge($validated, $guestValidated);
        }

        $selectedCartIds = collect($validated['selected_cart_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $cartItems = $this->cartQuery($request)
            ->whereIn('id', $selectedCartIds)
            ->with(['productSku.product'])
            ->orderByDesc('id')
            ->get();

        if ($cartItems->isEmpty() || $cartItems->count() !== $selectedCartIds->count()) {
            return back()->with('error', 'Vui lòng chọn sản phẩm hợp lệ trong giỏ hàng để thanh toán.');
        }

        if (! $user && filled($validated['voucher_code'] ?? null)) {
            return back()->with('error', 'Vui lòng đăng nhập để sử dụng voucher.');
        }

        $pricedCartItems = $this->buildPricedCartItems($cartItems);
        $pricing = $this->calculatePricing(
            $cartItems,
            $pricedCartItems,
            $validated['voucher_code'] ?? null,
            $user ? (int) $user->id : null
        );

        if (! empty($validated['voucher_code']) && ! $pricing['voucher']) {
            return back()->with('error', 'Voucher không hợp lệ hoặc đã hết lượt sử dụng.');
        }

        if (! empty($validated['voucher_code']) && (float) $pricing['discount_amount'] <= 0) {
            return back()->with('error', 'Voucher không áp dụng được cho giỏ hàng hiện tại.');
        }

        $customerPayload = $this->resolveCustomerPayload($validated, $user);
        $order = null;
        $payment = null;

        try {
            DB::transaction(function () use ($user, $cartItems, $pricedCartItems, $validated, $pricing, $request, $customerPayload, &$order, &$payment): void {
                $this->reserveStockForItems($cartItems);

                $orderData = [
                    'user_id' => $user ? (int) $user->id : null,
                    'order_code' => $this->makeOrderCode(),
                    'total_amount' => $pricing['total_before_discount'],
                    'discount_amount' => $pricing['discount_amount'],
                    'final_amount' => $pricing['final_amount'],
                    'status' => OrderStatus::PENDING->value,
                    'shipping_status' => ShippingStatus::PENDING->value,
                    'payment_method' => $validated['payment_method'],
                ];

                if (! $user) {
                    // guest checkout: persist guest_* fields on orders
                    $orderData['guest_name'] = $customerPayload['customer_name'];
                    $orderData['guest_email'] = $customerPayload['customer_email'];
                    $orderData['guest_phone'] = $customerPayload['customer_phone'];
                    $orderData['guest_address'] = $customerPayload['shipping_address'];
                }

                $order = Order::query()->create($orderData);

                $pricedItemsByCartId = $pricedCartItems->keyBy('id');

                foreach ($cartItems as $item) {
                    $pricedItem = $pricedItemsByCartId->get((int) $item->id, []);
                    $unitPrice = (float) ($pricedItem['unit_price'] ?? 0);

                    OrderItem::query()->create([
                        'order_id' => (int) $order->id,
                        'product_sku_id' => (int) $item->product_sku_id,
                        'product_name' => (string) data_get($item, 'productSku.product.name', 'Sản phẩm'),
                        'product_sku' => (string) data_get($item, 'productSku.sku', ''),
                        'product_size' => (string) data_get($item, 'productSku.size', ''),
                        'product_color' => (string) data_get($item, 'productSku.color', ''),
                        'quantity' => (int) $item->quantity,
                        'price' => round($unitPrice, 2),
                    ]);
                }

                if ($pricing['voucher']) {
                    OrderVoucher::query()->create([
                        'order_id' => (int) $order->id,
                        'voucher_id' => (int) $pricing['voucher']->id,
                        'discount_amount' => $pricing['discount_amount'],
                    ]);

                    // Đổi trạng thái voucher của user ngay khi tạo đơn (khi người dùng bấm thanh toán)
                    // để tránh trường hợp voucher bị dùng lại trong khi chờ thanh toán.
                    $this->applyVoucherUsageForOrder($order, $user ? (int) $user->id : null);
                }

                $payment = Payment::query()->create([
                    'order_id' => (int) $order->id,
                    'amount' => $pricing['final_amount'],
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => $this->makeTransactionCode(),
                    'status' => PaymentStatus::PENDING->value,
                ]);

                $this->cartQuery($request)
                    ->whereIn('id', $cartItems->pluck('id')->all())
                    ->delete();
            });
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Thanh toán thất bại, vui lòng thử lại sau.');
        }

        if (! $order || ! $payment) {
            return back()->with('error', 'Không thể khởi tạo giao dịch thanh toán.');
        }

        $toEmail = null;
        if (is_string($order->guest_email) && trim($order->guest_email) !== '') {
            $toEmail = $order->guest_email;
        } elseif (is_string($order->customer_email) && trim($order->customer_email) !== '') {
            $toEmail = $order->customer_email;
        } elseif ($order->user) {
            $toEmail = $order->user->email ?? null;
        }

        if (is_string($toEmail) && trim($toEmail) !== '') {
            try {
                Mail::to($toEmail)->send(
                    new OrderStatusNotificationEmail($order, OrderStatusNotificationEmail::EVENT_PLACED)
                );
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if (! $user) {
            $this->rememberGuestOrderCode($request, (string) $order->order_code);
        }

        if ($validated['payment_method'] === 'vnpay') {
            try {
                $paymentUrl = $this->buildVnpayPaymentUrl($request, $order, $payment);

                return redirect()->away($paymentUrl);
            } catch (Throwable $exception) {
                report($exception);
                $this->markPaymentAsFailed($order, $payment);

                return back()->with('error', 'Không thể khởi tạo thanh toán VNPay. Vui lòng thử lại sau.');
            }
        }

        if ($validated['payment_method'] === 'stripe') {
            try {
                $checkoutUrl = $this->createStripeCheckoutUrl($order, $payment);

                return redirect()->away($checkoutUrl);
            } catch (Throwable $exception) {
                report($exception);
                $this->markPaymentAsFailed($order, $payment);

                return back()->with('error', 'Không thể khởi tạo thanh toán Stripe. Vui lòng thử lại sau.');
            }
        }

        if (! $user) {
            return redirect()->route('user.orders', [
                'q' => (string) $order->order_code,
            ])->with('success', 'Đặt hàng thành công. Bạn có thể theo dõi đơn bằng mã đơn hàng vừa tạo.');
        }

        return redirect()->route('user.orders')->with('success', 'Đặt hàng thành công. Đơn hàng đang chờ xử lý.');
    }

    public function vnpayReturn(Request $request): RedirectResponse
    {
        $txnRef = (string) $request->query('vnp_TxnRef', '');

        if ($txnRef === '') {
            return redirect()->route('user.orders')->with('error', 'Không tìm thấy giao dịch VNPay.');
        }

        $payment = Payment::query()->where('transaction_id', $txnRef)->first();
        if (! $payment) {
            return redirect()->route('user.orders')->with('error', 'Giao dịch VNPay không tồn tại.');
        }

        $order = Order::query()->find($payment->order_id);
        if (! $order) {
            return redirect()->route('user.orders')->with('error', 'Không tìm thấy đơn hàng tương ứng.');
        }

        if (is_null($order->user_id)) {
            $this->rememberGuestOrderCode($request, (string) $order->order_code);
        }

        if (! $this->isValidVnpaySignature($request)) {
            $this->markPaymentAsFailed($order, $payment);

            return redirect()->route('user.orders')->with('error', 'Xác thực chữ ký VNPay thất bại.');
        }

        $isSuccess = (string) $request->query('vnp_ResponseCode', '') === '00'
            && (string) $request->query('vnp_TransactionStatus', '') === '00';

        if (! $isSuccess) {
            $this->markPaymentAsFailed($order, $payment);

            return redirect()->route('user.orders')->with('error', 'Thanh toán VNPay chưa thành công.');
        }

        $this->markPaymentAsPaid($order, $payment, $order->user_id > 0 ? (int) $order->user_id : null);

        return redirect()->route('user.orders')->with('success', 'Thanh toán VNPay thành công.');
    }

    public function stripeSuccess(Request $request): RedirectResponse
    {
        $sessionId = (string) $request->query('session_id', '');
        $orderCode = (string) $request->query('order_code', '');

        if ($sessionId === '' || $orderCode === '') {
            return redirect()->route('user.orders')->with('error', 'Thiếu thông tin giao dịch Stripe.');
        }

        $order = Order::query()->where('order_code', $orderCode)->first();
        if (! $order) {
            return redirect()->route('user.orders')->with('error', 'Không tìm thấy đơn hàng Stripe.');
        }

        if (is_null($order->user_id)) {
            $this->rememberGuestOrderCode($request, (string) $order->order_code);
        }

        $payment = Payment::query()->where('order_id', (int) $order->id)->first();
        if (! $payment) {
            return redirect()->route('user.orders')->with('error', 'Không tìm thấy giao dịch thanh toán Stripe.');
        }

        try {
            $client = new StripeClient($this->stripeSecretKey());
            $session = $client->checkout->sessions->retrieve($sessionId, []);
            $paid = (string) ($session->payment_status ?? '') === 'paid';
            $metadataOrderCode = (string) ($session->metadata->order_code ?? '');

            if ($metadataOrderCode !== $orderCode) {
                $this->markPaymentAsFailed($order, $payment);

                return redirect()->route('user.orders')->with('error', 'Dữ liệu giao dịch Stripe không khớp đơn hàng.');
            }

            if (! $paid) {
                $this->markPaymentAsFailed($order, $payment);

                return redirect()->route('user.orders')->with('error', 'Thanh toán Stripe chưa hoàn tất.');
            }

            $this->markPaymentAsPaid($order, $payment, $order->user_id > 0 ? (int) $order->user_id : null);

            return redirect()->route('user.orders')->with('success', 'Thanh toán Stripe thành công.');
        } catch (ApiErrorException|RuntimeException $exception) {
            report($exception);

            return redirect()->route('user.orders')->with('error', 'Không thể xác nhận giao dịch Stripe.');
        }
    }

    public function stripeCancel(Request $request): RedirectResponse
    {
        $orderCode = (string) $request->query('order_code', '');

        if ($orderCode !== '') {
            $order = Order::query()->where('order_code', $orderCode)->first();
            if ($order) {
                if (is_null($order->user_id)) {
                    $this->rememberGuestOrderCode($request, (string) $order->order_code);
                }

                $payment = Payment::query()->where('order_id', (int) $order->id)->first();
                if ($payment) {
                    $this->markPaymentAsFailed($order, $payment);
                }
            }
        }

        return redirect()->route('user.orders')->with('error', 'Bạn đã hủy thanh toán Stripe.');
    }

    public function retryPayment(Request $request, Order $order): RedirectResponse
    {
        if ($request->user()) {
            if ((int) $order->user_id !== (int) $request->user()->id) {
                abort(403);
            }
        } else {
            $guestOrderCodes = collect($request->session()->get('guest_order_codes', []))
                ->filter(fn ($code) => is_string($code) && trim($code) !== '')
                ->map(fn ($code) => strtoupper(trim((string) $code)))
                ->all();

            if (! in_array(strtoupper((string) $order->order_code), $guestOrderCodes, true)) {
                abort(403);
            }
        }

        $payment = Payment::query()->where('order_id', (int) $order->id)->first();
        if (! $payment) {
            return redirect()->route('user.orders')->with('error', 'Không tìm thấy giao dịch thanh toán.');
        }

        if (! in_array((string) $payment->status, [PaymentStatus::PENDING->value, PaymentStatus::FAILED->value], true)) {
            return redirect()->route('user.orders')->with('error', 'Chỉ có thể thanh toán lại cho giao dịch đang chờ hoặc đã thất bại.');
        }

        if ((string) $payment->status === PaymentStatus::FAILED->value) {
            try {
                DB::transaction(function () use ($order, $payment): void {
                    $payment->update([
                        'status' => PaymentStatus::PENDING->value,
                        'transaction_id' => $this->makeTransactionCode(),
                    ]);

                    if ((string) $order->status === OrderStatus::PAYMENT_FAILED->value) {
                        $order->update(['status' => OrderStatus::PENDING->value]);
                    }

                    $this->reserveStockForItems($order->items()->get());
                });
            } catch (RuntimeException $exception) {
                return redirect()->route('user.orders')->with('error', $exception->getMessage());
            } catch (Throwable $exception) {
                report($exception);

                return redirect()->route('user.orders')->with('error', 'Không thể khởi tạo lại thanh toán.');
            }
        }

        $method = (string) ($order->payment_method ?? '');
        if ($method === 'vnpay') {
            try {
                $paymentUrl = $this->buildVnpayPaymentUrl($request, $order, $payment);

                return redirect()->away($paymentUrl);
            } catch (Throwable $exception) {
                report($exception);
                $this->markPaymentAsFailed($order, $payment);

                return redirect()->route('user.orders')->with('error', 'Không thể khởi tạo thanh toán VNPay.');
            }
        }

        if ($method === 'stripe') {
            try {
                $checkoutUrl = $this->createStripeCheckoutUrl($order, $payment);

                return redirect()->away($checkoutUrl);
            } catch (Throwable $exception) {
                report($exception);
                $this->markPaymentAsFailed($order, $payment);

                return redirect()->route('user.orders')->with('error', 'Không thể khởi tạo thanh toán Stripe.');
            }
        }

        return redirect()->route('user.orders')->with('error', 'Phương thức thanh toán không hỗ trợ thanh toán lại.');
    }

    private function calculatePricing(Collection $cartItems, Collection $pricedCartItems, ?string $voucherCode, ?int $userId): array
    {
        $subtotal = round((float) $pricedCartItems->sum(fn (array $item) => (float) ($item['line_total'] ?? 0)), 2);
        $shippingFee = $subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::SHIPPING_FEE;

        $voucher = null;
        $discountAmount = 0.0;

        if (is_string($voucherCode) && trim($voucherCode) !== '') {
            $voucher = $this->resolveVoucher(trim($voucherCode), $userId);

            if ($voucher) {
                $discountAmount = $this->calculateVoucherDiscount($voucher, $cartItems, $pricedCartItems, $subtotal, $shippingFee);
            }
        }

        $totalBeforeDiscount = round($subtotal + $shippingFee, 2);
        $discountAmount = min($discountAmount, $totalBeforeDiscount);

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total_before_discount' => $totalBeforeDiscount,
            'discount_amount' => round($discountAmount, 2),
            'final_amount' => round(max($totalBeforeDiscount - $discountAmount, 0), 2),
            'voucher' => $voucher,
        ];
    }

    private function resolveVoucher(string $voucherCode, ?int $userId): ?Voucher
    {
        $voucher = Voucher::query()
            ->whereRaw('UPPER(code) = ?', [strtoupper($voucherCode)])
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function (Builder $query): void {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->first();

        if (! $voucher) {
            return null;
        }

        if (is_null($userId)) {
            return null;
        }

        $hasInWallet = UserVoucher::query()
            ->where('user_id', $userId)
            ->where('voucher_id', $voucher->id)
            ->where('status', VoucherStatus::UNUSED->value)
            ->exists();

        return $hasInWallet ? $voucher : null;
    }

    private function calculateVoucherDiscount(Voucher $voucher, Collection $cartItems, Collection $pricedCartItems, float $subtotal, float $shippingFee): float
    {
        if ($subtotal < (float) $voucher->min_order_value) {
            return 0.0;
        }

        if ($voucher->discount_type === 'shipping') {
            return min((float) $voucher->discount_value, $shippingFee);
        }

        $eligibleSubtotal = $this->eligibleSubtotalByScope($voucher, $cartItems, $pricedCartItems);
        if ($eligibleSubtotal <= 0) {
            return 0.0;
        }

        $discount = $voucher->discount_type === 'percent'
            ? ($eligibleSubtotal * (float) $voucher->discount_value) / 100
            : (float) $voucher->discount_value;

        if (! is_null($voucher->max_discount)) {
            $discount = min($discount, (float) $voucher->max_discount);
        }

        return min(round($discount, 2), $eligibleSubtotal);
    }

    private function eligibleSubtotalByScope(Voucher $voucher, Collection $cartItems, Collection $pricedCartItems): float
    {
        if ($voucher->category === 'all') {
            return (float) $pricedCartItems->sum(fn (array $item) => (float) ($item['line_total'] ?? 0));
        }

        return (float) $cartItems->sum(function (Cart $item) use ($voucher, $pricedCartItems): float {
            $product = $item->productSku?->product;
            if (! $product) {
                return 0.0;
            }

            $pricedItem = $pricedCartItems->firstWhere('id', (int) $item->id) ?? [];
            $lineTotal = (float) ($pricedItem['line_total'] ?? 0);

            $matched = match ($voucher->category) {
                'product' => (int) $product->id === (int) $voucher->product_id,
                'category' => (int) $product->category_id === (int) $voucher->category_id,
                'collection' => (int) $product->collection_id === (int) $voucher->collection_id,
                default => false,
            };

            return $matched ? $lineTotal : 0.0;
        });
    }

    private function makeOrderCode(): string
    {
        return 'OD'.now()->format('YmdHis').random_int(10, 99);
    }

    private function buildVnpayPaymentUrl(Request $request, Order $order, Payment $payment): string
    {
        $tmnCode = (string) config('services.vnpay.tmn_code');
        $hashSecret = (string) config('services.vnpay.hash_secret');
        $paymentUrl = (string) config('services.vnpay.payment_url');

        if ($tmnCode === '' || $hashSecret === '' || $paymentUrl === '') {
            throw new RuntimeException('VNPay is not configured.');
        }

        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $tmnCode,
            'vnp_Amount' => (string) ((int) round((float) $payment->amount * 100)),
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => (string) $payment->transaction_id,
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->order_code,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => 'vn',
            'vnp_ReturnUrl' => route('user.checkout.vnpay-return'),
            'vnp_IpAddr' => (string) $request->ip(),
            'vnp_CreateDate' => now()->format('YmdHis'),
        ];

        ksort($params);

        $hashData = [];
        $queryData = [];

        foreach ($params as $key => $value) {
            $hashData[] = $key.'='.urlencode((string) $value);
            $queryData[] = urlencode((string) $key).'='.urlencode((string) $value);
        }

        $secureHash = hash_hmac('sha512', implode('&', $hashData), $hashSecret);

        return rtrim($paymentUrl, '?')
            .'?'
            .implode('&', $queryData)
            .'&vnp_SecureHash='.$secureHash;
    }

    private function isValidVnpaySignature(Request $request): bool
    {
        $providedHash = (string) $request->query('vnp_SecureHash', '');
        $hashSecret = (string) config('services.vnpay.hash_secret');

        if ($providedHash === '' || $hashSecret === '') {
            return false;
        }

        $inputData = $request->query();
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);

        $hashData = [];
        foreach ($inputData as $key => $value) {
            if (str_starts_with((string) $key, 'vnp_')) {
                $hashData[] = $key.'='.urlencode((string) $value);
            }
        }

        $calculated = hash_hmac('sha512', implode('&', $hashData), $hashSecret);

        return hash_equals($calculated, $providedHash);
    }

    private function createStripeCheckoutUrl(Order $order, Payment $payment): string
    {
        $client = new StripeClient($this->stripeSecretKey());

        $session = $client->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'vnd',
                    'unit_amount' => (int) round((float) $payment->amount),
                    'product_data' => [
                        'name' => 'Thanh toan don hang '.$order->order_code,
                    ],
                ],
            ]],
            'metadata' => [
                'order_code' => (string) $order->order_code,
                'transaction_id' => (string) $payment->transaction_id,
            ],
            'success_url' => route('user.checkout.stripe-success', [
                'order_code' => (string) $order->order_code,
            ]).'&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('user.checkout.stripe-cancel', [
                'order_code' => (string) $order->order_code,
            ]),
        ]);

        if (empty($session->url)) {
            throw new RuntimeException('Stripe checkout URL is empty.');
        }

        return (string) $session->url;
    }

    private function stripeSecretKey(): string
    {
        $key = (string) config('services.stripe.secret');

        if ($key === '') {
            throw new RuntimeException('Stripe secret key is missing.');
        }

        return $key;
    }

    private function markPaymentAsPaid(Order $order, Payment $payment, ?int $userId): void
    {
        if ((string) $payment->status === PaymentStatus::PAID->value) {
            return;
        }

        $payment->update(['status' => PaymentStatus::PAID->value]);

        if ((string) $order->status === OrderStatus::PENDING->value || (string) $order->status === OrderStatus::PAYMENT_FAILED->value) {
            $order->update(['status' => OrderStatus::PROCESSING->value]);
        }

    }

    private function markPaymentAsFailed(Order $order, Payment $payment): void
    {
        if ((string) $payment->status === PaymentStatus::PAID->value || (string) $payment->status === PaymentStatus::FAILED->value) {
            return;
        }

        DB::transaction(function () use ($order, $payment): void {
            $payment->update(['status' => PaymentStatus::FAILED->value]);

            if ((string) $order->status === OrderStatus::PENDING->value) {
                $this->restoreStockForItems($order->items()->get());
                $order->update(['status' => OrderStatus::PAYMENT_FAILED->value]);
            }
        });
    }

    private function applyVoucherUsageForOrder(Order $order, ?int $userId): void
    {
        $orderVoucher = OrderVoucher::query()->where('order_id', (int) $order->id)->first();
        if (! $orderVoucher) {
            return;
        }

        Voucher::query()->where('id', (int) $orderVoucher->voucher_id)->increment('used_count');

        if (is_null($userId)) {
            return;
        }

        // Thử cập nhật một bản ghi 'unused' trước
        $affected = UserVoucher::query()
            ->where('user_id', $userId)
            ->where('voucher_id', (int) $orderVoucher->voucher_id)
            ->where('status', VoucherStatus::UNUSED->value)
            ->limit(1)
            ->update([
                'status' => VoucherStatus::USED->value,
                'used_at' => now(),
            ]);

        // Nếu không có bản ghi 'unused' (ví dụ trạng thái đã khác / dữ liệu không tương thích),
        // cập nhật bất kỳ bản ghi UserVoucher nào của user với voucher này (dự phòng, limit 1).
        if ($affected === 0) {
            UserVoucher::query()
                ->where('user_id', $userId)
                ->where('voucher_id', (int) $orderVoucher->voucher_id)
                ->limit(1)
                ->update([
                    'status' => VoucherStatus::USED->value,
                    'used_at' => now(),
                ]);
        }
    }

    private function resolveCustomerPayload(array $validated, $user): array
    {
        if ($user) {
            $customerName = trim((string) ($validated['customer_name'] ?? ''));

            return [
                'customer_name' => $customerName !== ''
                    ? $customerName
                    : (string) ($user->full_name ?: 'Khách hàng'),
                'customer_email' => (string) ($user->email ?? ''),
                'customer_phone' => (string) ($validated['customer_phone'] ?? $user->phone_number ?? ''),
                'shipping_address' => (string) ($validated['shipping_address'] ?? $user->address ?? ''),
            ];
        }

        return [
            'customer_name' => (string) ($validated['customer_name'] ?? ''),
            'customer_email' => (string) ($validated['customer_email'] ?? ''),
            'customer_phone' => (string) ($validated['customer_phone'] ?? ''),
            'shipping_address' => (string) ($validated['shipping_address'] ?? ''),
        ];
    }

    private function cartQuery(Request $request): Builder
    {
        return Cart::query()->where(function (Builder $query) use ($request): void {
            if ($request->user()) {
                $query->where('user_id', (int) $request->user()->id);

                return;
            }

            $query->whereNull('user_id')->where('session_id', $request->session()->getId());
        });
    }

    private function buildPricedCartItems(Collection $cartItems, ?Collection $flashSales = null): Collection
    {
        $flashSales = $flashSales ?? FlashSalePricing::activeSales();

        return $cartItems->map(function (Cart $item) use ($flashSales): array {
            $product = $item->productSku?->product;

            if ($product instanceof Products) {
                $product = FlashSalePricing::applyProduct($product, $flashSales);
            }

            $basePrice = $product instanceof Products ? (float) ($product->base_price ?? 0) : 0.0;
            $salePrice = $product instanceof Products ? FlashSalePricing::displayPrice($product) : 0.0;
            $unitPrice = $salePrice > 0 ? $salePrice : $basePrice;

            return [
                'id' => (int) $item->id,
                'product_id' => (int) ($product?->id ?? 0),
                'category_id' => (int) ($product?->category_id ?? 0),
                'collection_id' => (int) ($product?->collection_id ?? 0),
                'quantity' => (int) $item->quantity,
                'base_price' => $basePrice,
                'sale_price' => $salePrice > 0 && $salePrice < $basePrice ? $salePrice : null,
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * (int) $item->quantity, 2),
            ];
        });
    }

    private function makeTransactionCode(): string
    {
        return 'TXN'.now()->format('YmdHis').random_int(100, 999);
    }

    private function rememberGuestOrderCode(Request $request, string $orderCode): void
    {
        $existingCodes = collect($request->session()->get('guest_order_codes', []))
            ->filter(fn ($code) => is_string($code) && trim($code) !== '')
            ->push($orderCode)
            ->unique()
            ->take(-20)
            ->values()
            ->all();

        $request->session()->put('guest_order_codes', $existingCodes);
    }

    private function reserveStockForItems(Collection $items): void
    {
        $this->adjustStockForItems($items, -1);
    }

    private function restoreStockForItems(Collection $items): void
    {
        $this->adjustStockForItems($items, 1);
    }

    private function adjustStockForItems(Collection $items, int $direction): void
    {
        $normalizedItems = $items
            ->map(function ($item): array {
                return [
                    'product_sku_id' => (int) data_get($item, 'product_sku_id', 0),
                    'quantity' => max(1, (int) data_get($item, 'quantity', 1)),
                    'product_name' => (string) data_get($item, 'product_name', data_get($item, 'productSku.product.name', 'Sản phẩm')),
                ];
            })
            ->filter(fn (array $item): bool => $item['product_sku_id'] > 0)
            ->values();

        if ($normalizedItems->isEmpty()) {
            return;
        }

        $lockedSkus = ProductSkus::query()
            ->whereIn('id', $normalizedItems->pluck('product_sku_id')->all())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($normalizedItems as $item) {
            $sku = $lockedSkus->get($item['product_sku_id']);

            if (! $sku) {
                throw new RuntimeException('Không tìm thấy biến thể sản phẩm cho đơn hàng.');
            }

            $nextStock = (int) $sku->stock + ($direction * $item['quantity']);

            if ($nextStock < 0) {
                throw new RuntimeException('Sản phẩm '.$item['product_name'].' đã hết hàng hoặc không đủ tồn kho.');
            }

            if ($direction < 0) {
                $sku->decrement('stock', $item['quantity']);
            } elseif ($direction > 0) {
                $sku->increment('stock', $item['quantity']);
            }
        }
    }
}
