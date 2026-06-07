@extends('layouts.user-layout')
@section('title', 'Đơn hàng')

@section('content')
    @php
        use App\Enums\OrderStatus;
        use App\Enums\OrderReturnRequestStatus;
        use App\Enums\OrderReturnRequestType;
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

        $returnRequestStatusMap = array_reduce(
            OrderReturnRequestStatus::cases(),
            function ($carry, $status) {
                $carry[$status->value] = [
                    'label' => $status->label(),
                    'class' => $status->badgeClass(),
                ];
                return $carry;
            },
            [],
        );

        $returnRequestTypeMap = array_reduce(
            OrderReturnRequestType::cases(),
            function ($carry, $type) {
                $carry[$type->value] = [
                    'label' => $type->label(),
                    'class' => $type->badgeClass(),
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
                        $isClosedOrder = in_array(
                            $order->status,
                            [
                                OrderStatus::COMPLETED->value,
                                OrderStatus::RETURNED->value,
                                OrderStatus::EXCHANGED->value,
                            ],
                            true,
                        );
                        $canLeaveFeedback = ($isDelivered || $isClosedOrder) && !$order->feedback;
                        $canCancelOrder = !$isCancelled && !$isDelivered;
                        $returnRequest = $order->returnRequest;
                        $returnWindowStart = $isClosedOrder || $isDelivered ? $order->updated_at : null;
                        $returnWindowEnd = $returnWindowStart?->copy()->addDays(7);
                        $isReturnWindowExpired = $returnWindowEnd ? now()->gt($returnWindowEnd) : true;
                        $returnRequestStatus = $returnRequest?->status;
                        $isReturnRequestCompleted =
                            $returnRequestStatus &&
                            ($returnRequestStatus->value ?? (string) $returnRequestStatus) ===
                                OrderReturnRequestStatus::COMPLETED->value;
                        $hasActiveReturnRequest =
                            $returnRequestStatus &&
                            in_array(
                                $returnRequestStatus->value ?? (string) $returnRequestStatus,
                                [OrderReturnRequestStatus::PENDING->value, OrderReturnRequestStatus::APPROVED->value],
                                true,
                            );
                        $canRequestReturn = $isDelivered || $isCompleted;
                        $showReturnForm =
                            $canRequestReturn &&
                            !$isReturnWindowExpired &&
                            !$isReturnRequestCompleted &&
                            (!$returnRequest || !$hasActiveReturnRequest);

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
                                    <span
                                        class="font-semibold text-gray-800">{{ $order->user?->full_name ?? ($order->guest_name ?? 'N/A') }}</span>
                                </p>
                                <p><span class="text-gray-500">SĐT:</span>
                                    <span
                                        class="font-semibold text-gray-800">{{ $order->user?->phone_number ?? ($order->guest_phone ?? 'N/A') }}</span>
                                </p>
                                <p><span class="text-gray-500">Địa chỉ:</span>
                                    <span
                                        class="font-semibold text-gray-800">{{ $order->user?->address ?? ($order->guest_address ?? 'N/A') }}</span>
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
                                                {{ $item->product_name ?: $item->productSku?->product?->name ?: 'Sản phẩm' }}
                                            </p>
                                            <p class="text-xs text-gray-500">SKU:
                                                {{ $item->product_sku ?: $item->productSku?->sku ?? 'N/A' }} |
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
                                        {{ $order->feedback ? 'Bạn đã gửi feedback cho đơn hàng này.' : ($canLeaveFeedback ? 'Đơn này đã hoàn tất, bạn có thể gửi feedback ngay bên dưới.' : 'Feedback sẽ mở sau khi đơn hàng hoàn tất hoặc đổi/trả xong.') }}
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

                        <div class="mt-4 rounded-2xl border border-violet-100 bg-violet-50/60 p-4">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-violet-500">Đổi / trả hàng
                                    </p>
                                    <p class="mt-1 text-sm text-violet-700">
                                        @if ($isReturnRequestCompleted)
                                            Yêu cầu đổi/trả đã được xử lý hoàn tất.
                                        @elseif (!$canRequestReturn)
                                            Yêu cầu đổi/trả chỉ mở sau khi đơn đã giao hoặc hoàn thành.
                                        @elseif ($isReturnWindowExpired)
                                            Đơn đã quá hạn đổi/trả (7 ngày kể từ ngày hoàn thành).
                                        @else
                                            Bạn có thể gửi yêu cầu đổi/trả cho đơn này.
                                        @endif
                                    </p>
                                    @if ($returnWindowEnd)
                                        <p class="mt-1 text-xs text-violet-600">
                                            Hạn gửi yêu cầu: {{ $returnWindowEnd->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </div>

                                @if ($returnRequest)
                                    @php
                                        $returnStatusKey =
                                            $returnRequestStatus?->value ?? (string) $returnRequestStatus;
                                        $returnTypeKey =
                                            $returnRequest->request_type?->value ??
                                            (string) $returnRequest->request_type;
                                        $returnStatus = $returnRequestStatusMap[$returnStatusKey] ?? [
                                            'label' => ucfirst((string) $returnRequestStatus),
                                            'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        ];
                                        $returnType = $returnRequestTypeMap[$returnTypeKey] ?? [
                                            'label' => ucfirst((string) $returnRequest->request_type),
                                            'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        ];
                                    @endphp

                                    <span
                                        class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $returnStatus['class'] }}">
                                        {{ $returnStatus['label'] }}
                                    </span>
                                @endif
                            </div>

                            @if ($returnRequest)
                                @php
                                    $returnStatusKey = $returnRequestStatus?->value ?? (string) $returnRequestStatus;
                                    $returnTypeKey =
                                        $returnRequest->request_type?->value ?? (string) $returnRequest->request_type;
                                    $returnStatus = $returnRequestStatusMap[$returnStatusKey] ?? [
                                        'label' => ucfirst((string) $returnRequestStatus),
                                        'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    ];
                                    $returnType = $returnRequestTypeMap[$returnTypeKey] ?? [
                                        'label' => ucfirst((string) $returnRequest->request_type),
                                        'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    ];
                                @endphp

                                <div class="mt-4 rounded-xl border border-violet-100 bg-white p-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $returnType['class'] }}">{{ $returnType['label'] }}</span>
                                        <span
                                            class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $returnStatus['class'] }}">{{ $returnStatus['label'] }}</span>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-gray-800">{{ $returnRequest->reason }}</p>
                                    @if ($returnRequest->details)
                                        <p class="mt-2 text-sm text-gray-600 leading-6">{{ $returnRequest->details }}</p>
                                    @endif

                                    @if (!empty($returnRequest->evidence_images))
                                        <div class="mt-4">
                                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-violet-500">Ảnh
                                                minh chứng</p>
                                            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                                                @foreach ($returnRequest->evidence_images as $imagePath)
                                                    @php
                                                        $normalized = str_replace('\\', '/', trim((string) $imagePath));
                                                        if (
                                                            \Illuminate\Support\Str::startsWith($normalized, [
                                                                'http://',
                                                                'https://',
                                                            ])
                                                        ) {
                                                            $imageUrl = $normalized;
                                                        } elseif (
                                                            \Illuminate\Support\Str::startsWith($normalized, [
                                                                '/storage/',
                                                                'storage/',
                                                                '/uploads/',
                                                                'uploads/',
                                                                '/images/',
                                                                'images/',
                                                            ])
                                                        ) {
                                                            $imageUrl = asset(ltrim($normalized, '/'));
                                                        } else {
                                                            $imageUrl = asset('storage/' . ltrim($normalized, '/'));
                                                        }
                                                    @endphp

                                                    <a href="{{ $imageUrl }}" target="_blank"
                                                        class="group block overflow-hidden rounded-2xl border border-violet-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                                        <img src="{{ $imageUrl }}" alt="Ảnh minh chứng đổi/trả"
                                                            class="aspect-square h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($returnRequest->admin_note)
                                        <div class="mt-3 rounded-xl border border-violet-100 bg-violet-50 px-4 py-3">
                                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-violet-500">Ghi
                                                chú xử lý</p>
                                            <p class="mt-2 text-sm text-violet-900">{{ $returnRequest->admin_note }}</p>
                                        </div>
                                    @endif

                                    <p class="mt-3 text-xs text-gray-500">
                                        Gửi lúc {{ $returnRequest->created_at?->format('d/m/Y H:i') }}
                                        @if ($returnRequest->resolved_at)
                                            · Hoàn tất lúc {{ $returnRequest->resolved_at?->format('d/m/Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                            @elseif ($showReturnForm)
                                <form method="POST" action="{{ route('user.orders.return-request', $order) }}"
                                    enctype="multipart/form-data" class="mt-4 space-y-4">
                                    @csrf
                                    <input type="hidden" name="return_order_id" value="{{ $order->id }}">

                                    @if (old('return_order_id') == $order->id && $errors->any())
                                        <div
                                            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-[220px_minmax(0,1fr)]">
                                        <div>
                                            <label
                                                class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-violet-500">Loại
                                                yêu cầu</label>
                                            <select name="request_type"
                                                class="w-full rounded-xl border border-violet-200 px-3 py-2.5 text-sm focus:border-[#bc9c75] focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/10">
                                                @foreach ($returnRequestTypeMap as $value => $type)
                                                    <option value="{{ $value }}" @selected(old('request_type', $returnRequest?->request_type?->value ?? OrderReturnRequestType::RETURN->value) === $value)>
                                                        {{ $type['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label
                                                class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-violet-500">Lý
                                                do</label>
                                            <textarea name="reason" rows="3" placeholder="Ví dụ: size không vừa, sản phẩm lỗi, muốn đổi mẫu..."
                                                class="w-full rounded-xl border border-violet-200 px-3 py-2.5 text-sm focus:border-[#bc9c75] focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/10">{{ old('reason', $returnRequest?->reason ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-violet-500">Mô
                                            tả thêm</label>
                                        <textarea name="details" rows="4"
                                            placeholder="Mô tả thêm tình trạng sản phẩm, mong muốn đổi/trả, size cần đổi..."
                                            class="w-full rounded-xl border border-violet-200 px-3 py-2.5 text-sm focus:border-[#bc9c75] focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/10">{{ old('details', $returnRequest?->details ?? '') }}</textarea>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-violet-500">Ảnh
                                            minh chứng</label>
                                        <div class="space-y-2">
                                            <div
                                                class="relative overflow-hidden rounded-xl border border-dashed border-violet-300 bg-violet-50/40 px-4 py-4">
                                                <input type="file" name="evidence_images[]" accept="image/*" multiple
                                                    class="js-return-evidence-input absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                                    data-preview-target="return-preview-{{ $order->id }}"
                                                    data-file-name-target="return-file-name-{{ $order->id }}">

                                                <div
                                                    class="pointer-events-none flex items-center justify-center gap-3 text-violet-700">
                                                    <i class="ri-image-add-line text-lg"></i>
                                                    <p class="text-sm font-semibold">Chọn ảnh minh chứng đổi/trả</p>
                                                </div>
                                            </div>

                                            <p id="return-file-name-{{ $order->id }}" class="text-xs text-violet-600">
                                                Hỗ trợ JPG, PNG, WEBP. Tối đa 5 ảnh, mỗi ảnh tối đa 2MB.
                                            </p>

                                            <div id="return-preview-{{ $order->id }}"
                                                class="hidden gap-2 rounded-xl border border-violet-100 bg-white p-2">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 transition">
                                            <i class="ri-arrow-left-right-line"></i>
                                            Gửi yêu cầu đổi/trả
                                        </button>
                                    </div>
                                </form>
                            @elseif ($isReturnWindowExpired)
                                <div class="mt-4 rounded-xl border border-gray-200 bg-white p-4">
                                    <p class="text-sm font-semibold text-gray-800">Đã quá hạn đổi/trả.</p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Chính sách hiện tại chỉ cho phép tạo yêu cầu trong vòng 7 ngày kể từ ngày đơn được
                                        hoàn thành/giao thành công.
                                    </p>
                                </div>
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

    <script>
        (function() {
            const renderPreviews = (input) => {
                const previewId = input.getAttribute('data-preview-target');
                const fileNameId = input.getAttribute('data-file-name-target');
                const previewContainer = previewId ? document.getElementById(previewId) : null;
                const fileNameEl = fileNameId ? document.getElementById(fileNameId) : null;

                if (!previewContainer || !fileNameEl) {
                    return;
                }

                const files = Array.from(input.files || []);
                if (files.length === 0) {
                    previewContainer.innerHTML = '';
                    previewContainer.classList.add('hidden');
                    previewContainer.classList.remove('grid', 'grid-cols-2', 'sm:grid-cols-3');
                    fileNameEl.textContent = 'Hỗ trợ JPG, PNG, WEBP. Tối đa 5 ảnh, mỗi ảnh tối đa 2MB.';
                    return;
                }

                fileNameEl.textContent = `Đã chọn ${files.length} ảnh`;
                previewContainer.innerHTML = '';
                previewContainer.classList.remove('hidden');
                previewContainer.classList.add('grid', 'grid-cols-2', 'sm:grid-cols-3');

                files.forEach((file) => {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const wrapper = document.createElement('div');
                        wrapper.className =
                            'overflow-hidden rounded-lg border border-violet-100 bg-violet-50/30';

                        const img = document.createElement('img');
                        img.src = event.target?.result || '';
                        img.alt = file.name;
                        img.className = 'h-24 w-full object-cover';

                        wrapper.appendChild(img);
                        previewContainer.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            };

            document.querySelectorAll('.js-return-evidence-input').forEach((input) => {
                input.addEventListener('change', () => renderPreviews(input));
            });
        })();
    </script>
@endsection
