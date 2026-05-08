@extends('layouts.user-layout')
@section('title', 'Đơn hàng')

@section('content')
    @php
        use App\Enums\OrderStatus;
        use App\Enums\ShippingStatus;
        use App\Enums\PaymentStatus;

        $orderStatusMap = array_reduce(
            OrderStatus::cases(),
            function ($carry, $status) {
                $carry[$status->value] = [
                    'label' => $status->label(),
                    'class' => $status->badgeClass(),
                ];
                return $carry;
            },
            [],
        );

        $shippingStatusMap = array_reduce(
            ShippingStatus::cases(),
            function ($carry, $status) {
                $carry[$status->value] = [
                    'label' => $status->label(),
                    'class' => $status->badgeClass(),
                ];
                return $carry;
            },
            [],
        );

        $paymentStatusMap = array_reduce(
            PaymentStatus::cases(),
            function ($carry, $status) {
                $carry[$status->value] = [
                    'label' => $status->label(),
                    'class' => $status->badgeClass(),
                ];
                return $carry;
            },
            [],
        );
    @endphp

    <div class="max-w-7xl mx-auto px-4 py-10 md:py-12">
        <div class="flex flex-wrap items-end justify-between gap-3 mb-5">
            <div>
                <h1 class="text-2xl md:text-3xl font-black uppercase tracking-wide text-gray-900">Đơn hàng của bạn</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $isGuest ? 'Bạn có thể tra cứu đơn bằng mã đơn hàng, email hoặc số điện thoại.' : 'Theo dõi trạng thái thanh toán và vận chuyển mới nhất.' }}
                </p>
            </div>

            <a wire:navigate href="{{ route('user.product') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-[#e7dccb] bg-white px-4 py-2 text-sm font-semibold text-[#9d7a4a] hover:border-[#bc9c75] hover:text-[#8a6a3f] transition">
                <i class="ri-shopping-bag-line"></i>
                Tiếp tục mua sắm
            </a>
        </div>

        @if (session('success'))
            <div
                class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('user.orders') }}"
            class="mb-7 rounded-2xl border border-[#ece2d3] bg-white p-4 md:p-5">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="q" value="{{ $searchKeyword ?? '' }}"
                        placeholder="Tìm theo mã đơn hàng, email hoặc số điện thoại"
                        class="w-full rounded-xl border border-gray-300 py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-[#bc9c75] focus:border-[#bc9c75]">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-xl bg-[#bc9c75] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#a88966] transition">
                        Tìm kiếm
                    </button>
                    @if (!empty($searchKeyword))
                        <a href="{{ route('user.orders') }}"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Xóa lọc
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if ($orders->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center">
                <i class="ri-file-list-3-line text-6xl text-gray-300"></i>
                <h2 class="mt-4 text-xl font-bold text-gray-800">
                    {{ !empty($searchKeyword) ? 'Không tìm thấy đơn hàng phù hợp' : 'Chưa có đơn hàng nào' }}
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    {{ !empty($searchKeyword)
                        ? 'Hãy thử lại bằng mã đơn hàng, email hoặc số điện thoại khác.'
                        : 'Khi bạn đặt hàng thành công, trạng thái đơn sẽ hiển thị tại đây.' }}
                </p>
            </div>
        @else
            <div class="space-y-5">
                @foreach ($orders as $order)
                    @php
                        $isCancelled = $order->status === OrderStatus::CANCELLED->value;
                        $isDelivered = $order->shipping_status === ShippingStatus::DELIVERED->value;
                        $isCompleted = $order->status === OrderStatus::COMPLETED->value;
                        $canLeaveFeedback = ($isDelivered || $isCompleted) && !$order->feedback;
                        $canCancelOrder = !$isCancelled && !$isDelivered;

                        $orderStatus = $orderStatusMap[$order->status] ?? [
                            'label' => ucfirst((string) $order->status),
                            'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                        ];

                        $shippingStatus = $shippingStatusMap[$order->shipping_status] ?? [
                            'label' => ucfirst((string) $order->shipping_status),
                            'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                        ];

                        $payment = $order->payment;
                        $paymentStatus = $paymentStatusMap[$payment?->status] ?? [
                            'label' => $payment ? ucfirst((string) $payment->status) : 'Chưa tạo giao dịch',
                            'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                        ];
                    @endphp

                    <article class="rounded-2xl border border-[#ece2d3] bg-white p-4 md:p-5 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-gray-400">Mã đơn hàng</p>
                                <h3 class="text-lg md:text-xl font-black text-gray-900 mt-1">{{ $order->order_code }}</h3>
                                <p class="text-xs text-gray-500 mt-1">Đặt lúc:
                                    {{ optional($order->created_at)->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $orderStatus['class'] }}">
                                    {{ $orderStatus['label'] }}
                                </span>
                                <span
                                    class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $paymentStatus['class'] }}">
                                    {{ $paymentStatus['label'] }}
                                </span>
                                <span
                                    class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $shippingStatus['class'] }}">
                                    {{ $shippingStatus['label'] }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 space-y-1.5">
                                <p><span class="text-gray-500">Người nhận:</span>
                                    <span class="font-semibold text-gray-800">{{ $order->customer_name ?: 'N/A' }}</span>
                                </p>
                                <p><span class="text-gray-500">SĐT:</span>
                                    <span class="font-semibold text-gray-800">{{ $order->customer_phone ?: 'N/A' }}</span>
                                </p>
                                <p><span class="text-gray-500">Địa chỉ:</span>
                                    <span
                                        class="font-semibold text-gray-800">{{ $order->shipping_address ?: 'N/A' }}</span>
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 space-y-1.5">
                                <p><span class="text-gray-500">Phương thức thanh toán:</span>
                                    <span class="font-semibold text-gray-800 uppercase">{{ $order->payment_method }}</span>
                                </p>
                                <p><span class="text-gray-500">Mã giao dịch:</span>
                                    <span
                                        class="font-semibold text-gray-800">{{ $payment?->transaction_id ?: 'N/A' }}</span>
                                </p>
                                <p><span class="text-gray-500">Tổng thanh toán:</span>
                                    <span
                                        class="font-black text-[#bc9c75]">{{ number_format((float) $order->final_amount, 0, ',', '.') }}đ</span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-gray-400 mb-2">Sản phẩm</p>

                            <div class="space-y-2">
                                @foreach ($order->items->take(3) as $item)
                                    <div
                                        class="flex items-center justify-between gap-3 rounded-xl border border-gray-100 px-3 py-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate">
                                                {{ $item->productSku?->product?->name ?? 'Sản phẩm không tồn tại' }}</p>
                                            <p class="text-xs text-gray-500">SKU: {{ $item->productSku?->sku ?? 'N/A' }} |
                                                SL:
                                                {{ $item->quantity }}</p>
                                        </div>
                                        <div class="text-sm font-bold text-gray-800 whitespace-nowrap">
                                            {{ number_format((float) $item->price, 0, ',', '.') }}đ
                                        </div>
                                    </div>
                                @endforeach

                                @if ($order->items->count() > 3)
                                    <p class="text-xs text-gray-500">+ {{ $order->items->count() - 3 }} sản phẩm khác</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border border-gray-100 bg-[#fcfaf8] p-4">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-gray-400">Feedback đơn hàng
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $order->feedback ? 'Bạn đã gửi feedback cho đơn hàng này.' : ($canLeaveFeedback ? 'Đơn này đã hoàn tất, bạn có thể gửi feedback ngay bên dưới.' : 'Feedback sẽ mở sau khi đơn hàng hoàn tất.') }}
                                    </p>
                                </div>

                                @if ($order->feedback)
                                    <span
                                        class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $order->feedback->rating }}/5
                                    </span>
                                @endif
                            </div>

                            @if ($order->feedback)
                                <div class="mt-4 rounded-xl border border-emerald-100 bg-white p-4">
                                    <p class="text-sm font-semibold text-gray-800">{{ $order->feedback->content }}</p>
                                    <p class="mt-2 text-xs text-gray-500">Đã gửi lúc
                                        {{ $order->feedback->created_at?->format('d/m/Y H:i') }}</p>
                                </div>
                            @elseif ($canLeaveFeedback)
                                <form method="POST" action="{{ route('user.orders.feedback', $order) }}"
                                    class="mt-4 space-y-4">
                                    @csrf
                                    <input type="hidden" name="feedback_order_id" value="{{ $order->id }}">

                                    @if (old('feedback_order_id') == $order->id && $errors->any())
                                        <div
                                            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-[180px_minmax(0,1fr)]">
                                        <div>
                                            <label
                                                class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-gray-400">Số
                                                sao</label>
                                            <select name="rating"
                                                class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-[#bc9c75] focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/10">
                                                @for ($star = 5; $star >= 1; $star--)
                                                    <option value="{{ $star }}" @selected((string) old('rating', '5') === (string) $star)>
                                                        {{ $star }} sao</option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div>
                                            <label
                                                class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-gray-400">Nội
                                                dung feedback</label>
                                            <textarea name="content" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về đơn hàng này..."
                                                class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-[#bc9c75] focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/10">{{ old('content') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-xl bg-[#bc9c75] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#a88966] transition">
                                            <i class="ri-message-2-line"></i>
                                            Gửi feedback
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>

                        <div class="mt-4 flex items-center justify-end gap-2">
                            @if ($canCancelOrder)
                                <form method="POST" action="{{ route('user.orders.cancel', $order) }}"
                                    onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?')">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-100 transition">
                                        <i class="ri-close-circle-line"></i>
                                        Hủy đơn
                                    </button>
                                </form>
                            @else
                                <span
                                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-500">
                                    <i class="ri-lock-line"></i>
                                    Đơn này không thể thao tác
                                </span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
