@php
    use App\Enums\OrderReturnRequestStatus;
    use App\Enums\OrderStatus;
    use App\Enums\ShippingStatus;
    use App\Enums\PaymentStatus;
@endphp

<div class="space-y-6">
    @if (session('success'))
        <div
            class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm lg:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-violet-500">Quản lý đơn hàng</p>
                <h3 class="mt-1 text-lg font-black text-gray-800">Theo dõi đơn, thanh toán và vận chuyển</h3>
            </div>

            <a href="{{ route('admin.return-requests') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-4 py-2 text-xs font-semibold text-violet-700 hover:bg-violet-100 transition">
                <i class="fa-solid fa-rotate-left"></i>
                Xem đổi / trả
            </a>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-gray-100 bg-linear-to-br from-slate-50 to-white p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-gray-400">Tổng đơn</p>
                <h3 class="mt-2 text-2xl font-black text-gray-800">{{ number_format($summary['total']) }}</h3>
                <p class="mt-1 text-xs text-gray-500">Toàn bộ đơn trong hệ thống</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-amber-500">Chờ xử lý</p>
                <h3 class="mt-2 text-2xl font-black text-amber-700">{{ number_format($summary['pending']) }}</h3>
                <p class="mt-1 text-xs text-amber-600">Cần xác nhận sớm</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-blue-500">Đang xử lý</p>
                <h3 class="mt-2 text-2xl font-black text-blue-700">{{ number_format($summary['processing']) }}</h3>
                <p class="mt-1 text-xs text-blue-600">Đang đóng gói và chuẩn bị giao</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-emerald-500">Hoàn thành</p>
                <h3 class="mt-2 text-2xl font-black text-emerald-700">{{ number_format($summary['completed']) }}</h3>
                <p class="mt-1 text-xs text-emerald-600">Đã giao thành công</p>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-rose-500">Lỗi thanh toán</p>
                <h3 class="mt-2 text-2xl font-black text-rose-700">{{ number_format($summary['payment_failed']) }}</h3>
                <p class="mt-1 text-xs text-rose-600">Cần kiểm tra lại giao dịch</p>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-gray-100 bg-white p-4 shadow-sm lg:p-6">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-[0.12em] text-gray-600">Bộ lọc nâng cao</h3>
            <button type="button" wire:click="resetFilters"
                class="text-xs font-bold text-[#bc9c75] hover:text-[#9c7747] transition">Xóa bộ lọc</button>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
            <input type="text" wire:model.live.debounce.450ms="q" autocomplete="off"
                placeholder="Mã đơn / khách hàng / SĐT"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">

            <select wire:model.live="status"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">
                <option value="">Tất cả trạng thái đơn</option>
                @foreach ($orderStatuses as $item)
                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="shipping_status"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">
                <option value="">Tất cả vận chuyển</option>
                @foreach ($shippingStatuses as $item)
                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="payment_status"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">
                <option value="">Tất cả thanh toán</option>
                @foreach ($paymentStatuses as $item)
                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="return_status"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">
                <option value="">Tất cả đổi/trả</option>
                <option value="has_return">Có yêu cầu đổi/trả</option>
                @foreach (OrderReturnRequestStatus::cases() as $item)
                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                @endforeach
            </select>

            <div class="flex items-center justify-end">
                <span wire:loading wire:target="q,status,shipping_status,payment_status,return_status"
                    class="rounded-full bg-gray-100 px-3 py-1 text-[11px] font-semibold text-gray-500">Đang
                    lọc...</span>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            @if ($q !== '')
                <span
                    class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">
                    Từ khóa: {{ $q }}
                </span>
            @endif
            @if ($statusLabel)
                <span
                    class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-[11px] font-semibold text-amber-700">
                    Trạng thái đơn: {{ $statusLabel }}
                </span>
            @endif
            @if ($shippingStatusLabel)
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-[11px] font-semibold text-blue-700">
                    Vận chuyển: {{ $shippingStatusLabel }}
                </span>
            @endif
            @if ($paymentStatusLabel)
                <span
                    class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">
                    Thanh toán: {{ $paymentStatusLabel }}
                </span>
            @endif
            @if ($returnStatusLabel)
                <span
                    class="inline-flex items-center rounded-full bg-violet-100 px-3 py-1 text-[11px] font-semibold text-violet-700">
                    Đổi/trả: {{ $returnStatusLabel }}
                </span>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 lg:px-6">
            <h3 class="text-sm font-black uppercase tracking-[0.12em] text-gray-600">Danh sách đơn hàng</h3>
            <p class="text-xs text-gray-400">Hiển thị {{ $orders->count() }} / {{ number_format($orders->total()) }}
                đơn</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-black uppercase tracking-wider">Mã đơn</th>
                        <th class="px-4 py-3 text-left text-[11px] font-black uppercase tracking-wider">Khách hàng</th>
                        <th class="px-4 py-3 text-left text-[11px] font-black uppercase tracking-wider">Thanh toán</th>
                        <th class="px-4 py-3 text-left text-[11px] font-black uppercase tracking-wider">Trạng thái</th>
                        <th class="px-4 py-3 text-left text-[11px] font-black uppercase tracking-wider">Cập nhật</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($orders as $order)
                        <tr wire:key="order-row-{{ $order->id }}" class="hover:bg-[#fcfaf8] transition-colors">
                            <td class="px-4 py-3 align-top">
                                <p class="font-bold text-gray-800">{{ $order->order_code }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">{{ $order->created_at?->format('d/m/Y H:i') }}
                                </p>
                                <p
                                    class="mt-1 inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                    {{ $order->items->sum('quantity') }} sản phẩm
                                </p>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <p class="font-semibold text-gray-800">
                                    {{ $order->user?->full_name ?? ($order->guest_name ?? 'Khách vãng lai') }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ $order->user?->phone_number ?? ($order->guest_phone ?? 'N/A') }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ $order->user?->email ?? ($order->guest_email ?? 'N/A') }}</p>
                                <p class="mt-1 line-clamp-2 text-xs text-gray-500">
                                    {{ $order->user?->address ?? ($order->guest_address ?? 'N/A') }}</p>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                    {{ $order->payment_method }}</p>
                                <p class="mt-0.5 text-base font-black text-[#bc9c75]">
                                    {{ number_format((float) $order->final_amount, 0, ',', '.') }}đ</p>
                                <p class="mt-1 text-xs text-gray-500">Mã GD:
                                    {{ $order->payment?->transaction_id ?: 'N/A' }}</p>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="space-y-1.5">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ OrderStatus::from($order->status)->badgeClass() }}">
                                        Đơn: {{ OrderStatus::from($order->status)->label() }}
                                    </span>
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ ShippingStatus::from($order->shipping_status)->badgeClass() }}">
                                        Giao: {{ ShippingStatus::from($order->shipping_status)->label() }}
                                    </span>
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->payment?->status ? PaymentStatus::from($order->payment->status)->badgeClass() : 'bg-slate-100 text-slate-700' }}">
                                        TT:
                                        {{ $order->payment?->status ? PaymentStatus::from($order->payment->status)->label() : 'Chưa tạo giao dịch' }}
                                    </span>
                                    @php
                                        $feedback = $order->feedback->first();
                                    @endphp
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $feedback ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-600' }}">
                                        FB: {{ $feedback ? $feedback->rating . '/5' : 'Chưa có' }}
                                    </span>
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->returnRequest ? $order->returnRequest->status?->badgeClass() ?? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-600' }}">
                                        Đổi/trả:
                                        {{ $order->returnRequest ? $order->returnRequest->status?->label() ?? 'N/A' : 'Chưa có' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top">
                                @php
                                    $isCompletedOrder = in_array(
                                        $order->status,
                                        [
                                            OrderStatus::COMPLETED->value,
                                            OrderStatus::RETURNED->value,
                                            OrderStatus::EXCHANGED->value,
                                        ],
                                        true,
                                    );
                                    $isCancelledOrder = $order->status === OrderStatus::CANCELLED->value;
                                    $isLockedOrder = $isCompletedOrder || $isCancelledOrder;

                                    $orderModalData = [
                                        'order_code' => $order->order_code,
                                        'created_at' => $order->created_at?->format('d/m/Y H:i'),
                                        'status' => OrderStatus::from($order->status)->label(),
                                        'shipping_status' => ShippingStatus::from($order->shipping_status)->label(),
                                        'payment_status' => $order->payment?->status
                                            ? PaymentStatus::from($order->payment->status)->label()
                                            : 'Chưa tạo giao dịch',
                                        'feedback' => ($feedback = $order->feedback->first())
                                            ? [
                                                'rating' => (int) $feedback->rating,
                                                'content' => $feedback->content,
                                            ]
                                            : null,
                                        'payment_method' => strtoupper((string) $order->payment_method),
                                        'transaction_id' => $order->payment?->transaction_id ?: 'N/A',
                                        'customer_name' =>
                                            $order->user?->full_name ?? ($order->guest_name ?? 'Khách vãng lai'),
                                        'customer_phone' =>
                                            $order->user?->phone_number ?? ($order->guest_phone ?? 'N/A'),
                                        'customer_email' => $order->user?->email ?? ($order->guest_email ?? 'N/A'),
                                        'shipping_address' =>
                                            $order->user?->address ?? ($order->guest_address ?? 'N/A'),
                                        'total_amount' =>
                                            number_format((float) $order->total_amount, 0, ',', '.') . 'đ',
                                        'discount_amount' =>
                                            number_format((float) $order->discount_amount, 0, ',', '.') . 'đ',
                                        'final_amount' =>
                                            number_format((float) $order->final_amount, 0, ',', '.') . 'đ',
                                        'items' => $order->items
                                            ->map(function ($item) {
                                                return [
                                                    'product_name' =>
                                                        $item->product_name ?:
                                                        $item->productSku?->product?->name ?:
                                                        'Sản phẩm',
                                                    'sku' => $item->product_sku ?: $item->productSku?->sku ?: 'N/A',
                                                    'size' => $item->product_size ?: $item->productSku?->size ?: '-',
                                                    'color' => $item->product_color ?: $item->productSku?->color ?: '-',
                                                    'quantity' => (int) $item->quantity,
                                                    'price' => number_format((float) $item->price, 0, ',', '.') . 'đ',
                                                ];
                                            })
                                            ->values()
                                            ->all(),
                                    ];
                                @endphp

                                <div class="mb-2">
                                    <button type="button"
                                        class="w-full rounded-xl border border-[#d8c2a1] px-3 py-2 text-xs font-semibold text-[#9c7747] hover:bg-[#fdf8f2] transition"
                                        data-order='@json($orderModalData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
                                        onclick="openOrderModalFromButton(this)">
                                        Xem chi tiết
                                    </button>
                                </div>

                                <form action="{{ route('admin.orders.update', $order) }}" method="POST"
                                    class="space-y-2 mt-2">
                                    @csrf
                                    @method('PUT')

                                    <select name="status" @disabled($isLockedOrder)
                                        class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-xs focus:outline-none focus:border-[#bc9c75] disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500">
                                        @foreach ($orderStatuses as $item)
                                            <option value="{{ $item->value }}" @selected($order->status === $item->value)>
                                                Đơn: {{ $item->label() }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="shipping_status" @disabled($isLockedOrder)
                                        class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-xs focus:outline-none focus:border-[#bc9c75] disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500">
                                        @foreach ($shippingStatuses as $item)
                                            <option value="{{ $item->value }}" @selected($order->shipping_status === $item->value)>
                                                Giao: {{ $item->label() }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div
                                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-2 py-1.5 text-xs text-gray-600">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-semibold uppercase tracking-wide text-gray-500">Thanh
                                                toán</span>
                                            <span
                                                class="inline-flex rounded-full px-2 py-0.5 font-semibold {{ $order->payment?->status ? PaymentStatus::from($order->payment->status)->badgeClass() : 'bg-slate-100 text-slate-700' }}">
                                                {{ $order->payment?->status ? PaymentStatus::from($order->payment->status)->label() : 'Chưa tạo giao dịch' }}
                                            </span>
                                        </div>
                                    </div>

                                    @if ($isLockedOrder)
                                        <div
                                            class="w-full rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-center text-[11px] font-semibold text-emerald-700">
                                            {{ $isCancelledOrder ? 'Đơn đã hủy' : 'Đơn đã hoàn thành' }}
                                        </div>
                                    @else
                                        <button type="submit"
                                            class="w-full rounded-xl bg-linear-to-r from-[#bc9c75] to-[#a68560] px-3 py-2 text-xs font-semibold text-white hover:shadow-md transition">
                                            Lưu
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                Không có đơn hàng phù hợp bộ lọc hiện tại.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-4 py-3">
            {{ $orders->links() }}
        </div>
    </div>

    <script>
        function openOrderModalFromButton(button) {
            const raw = button.getAttribute('data-order');
            if (!raw) return;

            let data;
            try {
                data = JSON.parse(raw);
            } catch (error) {
                return;
            }

            const modal = document.getElementById('order-detail-modal');
            if (!modal) return;

            modal.querySelector('[data-order-code]').textContent = data.order_code || '-';
            modal.querySelector('[data-order-created-at]').textContent = data.created_at || '-';
            modal.querySelector('[data-order-status]').textContent = data.status || '-';
            modal.querySelector('[data-order-shipping-status]').textContent = data.shipping_status || '-';
            modal.querySelector('[data-order-payment-status]').textContent = data.payment_status || '-';
            modal.querySelector('[data-order-payment-method]').textContent = data.payment_method || '-';
            modal.querySelector('[data-order-transaction-id]').textContent = data.transaction_id || '-';
            modal.querySelector('[data-order-feedback]').textContent = data.feedback ?
                ((data.feedback.rating || '-') + '/5 - ' + (data.feedback.content || '')) :
                'Chưa có feedback';

            modal.querySelector('[data-order-customer-name]').textContent = data.customer_name || '-';
            modal.querySelector('[data-order-customer-phone]').textContent = data.customer_phone || '-';
            modal.querySelector('[data-order-customer-email]').textContent = data.customer_email || '-';
            modal.querySelector('[data-order-shipping-address]').textContent = data.shipping_address || '-';

            modal.querySelector('[data-order-total]').textContent = data.total_amount || '0đ';
            modal.querySelector('[data-order-discount]').textContent = data.discount_amount || '0đ';
            modal.querySelector('[data-order-final]').textContent = data.final_amount || '0đ';

            const itemsBody = modal.querySelector('[data-order-items]');
            itemsBody.innerHTML = '';

            const items = Array.isArray(data.items) ? data.items : [];
            if (items.length === 0) {
                itemsBody.innerHTML =
                    '<tr><td colspan="5" class="px-3 py-3 text-center text-xs text-gray-500">Không có sản phẩm trong đơn.</td></tr>';
            } else {
                items.forEach((item) => {
                    const row = document.createElement('tr');
                    row.innerHTML =
                        '<td class="px-3 py-2 text-xs text-gray-800">' + (item.product_name || '-') + '</td>' +
                        '<td class="px-3 py-2 text-xs text-gray-600">' + (item.sku || '-') + '</td>' +
                        '<td class="px-3 py-2 text-xs text-gray-600">' + (item.size || '-') + '/' + (item.color ||
                            '-') + '</td>' +
                        '<td class="px-3 py-2 text-xs text-gray-700 text-center">' + (item.quantity || 0) +
                        '</td>' +
                        '<td class="px-3 py-2 text-xs text-gray-700 text-right">' + (item.price || '0đ') + '</td>';
                    itemsBody.appendChild(row);
                });
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeOrderModal() {
            const modal = document.getElementById('order-detail-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeOrderModal();
            }
        });
    </script>

    <div id="order-detail-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-6xl rounded-4xl shadow-2xl overflow-hidden relative">
            <button type="button" onclick="closeOrderModal()"
                class="absolute top-5 right-5 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center z-20 transition-colors">
                <i class="fa-solid fa-xmark text-gray-500"></i>
            </button>

            <div class="grid grid-cols-1 lg:grid-cols-[320px_minmax(0,1fr)]">
                <div
                    class="p-8 bg-linear-to-br from-[#bc9c75] to-[#8d7558] text-white flex flex-col justify-between min-h-140">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Mã đơn hàng</p>
                        <p class="text-2xl font-black tracking-tight mt-2" data-order-code>-</p>
                        <p class="text-[11px] text-white/85 mt-1" data-order-created-at>-</p>
                    </div>

                    <div class="space-y-3 py-6">
                        <div class="rounded-2xl bg-white/15 border border-white/25 p-3">
                            <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Trạng thái đơn</p>
                            <p class="text-sm font-black mt-1" data-order-status>-</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 border border-white/25 p-3">
                            <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Vận chuyển</p>
                            <p class="text-sm font-black mt-1" data-order-shipping-status>-</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 border border-white/25 p-3">
                            <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Thanh toán</p>
                            <p class="text-sm font-black mt-1" data-order-payment-status>-</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 border border-white/25 p-3">
                            <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Feedback</p>
                            <p class="text-sm font-black mt-1" data-order-feedback>Chưa có feedback</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Phương thức thanh toán</p>
                        <p class="text-sm font-bold" data-order-payment-method>-</p>
                        <p class="text-xs text-white/85">Mã giao dịch: <span data-order-transaction-id>-</span></p>
                    </div>
                </div>

                <div class="p-8 max-h-[85vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-5">
                        <div class="rounded-2xl border border-gray-100 p-4 bg-[#fcfaf8]">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.16em] mb-2">Thông tin
                                khách hàng</p>
                            <p><span class="text-gray-500">Tên:</span> <span class="font-semibold"
                                    data-order-customer-name>-</span></p>
                            <p class="mt-1"><span class="text-gray-500">SĐT:</span> <span class="font-semibold"
                                    data-order-customer-phone>-</span></p>
                            <p class="mt-1"><span class="text-gray-500">Email:</span> <span class="font-semibold"
                                    data-order-customer-email>-</span></p>
                            <p class="mt-1"><span class="text-gray-500">Địa chỉ:</span> <span class="font-semibold"
                                    data-order-shipping-address>-</span></p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 p-4 bg-[#fcfaf8]">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.16em] mb-2">Tổng kết
                                thanh toán</p>
                            <p><span class="text-gray-500">Tổng gốc:</span> <span class="font-semibold"
                                    data-order-total>0đ</span></p>
                            <p class="mt-1"><span class="text-gray-500">Giảm giá:</span> <span
                                    class="font-semibold" data-order-discount>0đ</span></p>
                            <p class="mt-2 pt-2 border-t border-gray-200"><span class="text-gray-600">Thành
                                    tiền:</span> <span class="font-black text-[#bc9c75]" data-order-final>0đ</span></p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 overflow-hidden">
                        <div
                            class="px-4 py-3 bg-[#fcfaf8] text-[10px] font-black text-gray-500 uppercase tracking-[0.16em]">
                            Sản phẩm trong đơn</div>
                        <table class="w-full text-sm">
                            <thead class="bg-white border-b border-gray-100">
                                <tr>
                                    <th
                                        class="text-left px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-[0.12em]">
                                        Sản phẩm</th>
                                    <th
                                        class="text-left px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-[0.12em]">
                                        SKU</th>
                                    <th
                                        class="text-left px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-[0.12em]">
                                        Size/Màu</th>
                                    <th
                                        class="text-center px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-[0.12em]">
                                        SL</th>
                                    <th
                                        class="text-right px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-[0.12em]">
                                        Giá</th>
                                </tr>
                            </thead>
                            <tbody data-order-items></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
