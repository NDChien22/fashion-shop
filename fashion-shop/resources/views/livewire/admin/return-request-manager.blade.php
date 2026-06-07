@php
    use App\Enums\OrderReturnRequestStatus;
    use App\Enums\OrderReturnRequestType;
    use App\Enums\OrderStatus;
    use App\Enums\PaymentStatus;
    use App\Enums\ShippingStatus;
@endphp

@php
    $statusMap = collect($statusOptions)->mapWithKeys(function ($status) {
        return [$status->value => ['label' => $status->label(), 'class' => $status->badgeClass()]];
    });

    $typeMap = collect($typeOptions)->mapWithKeys(function ($type) {
        return [$type->value => ['label' => $type->label(), 'class' => $type->badgeClass()]];
    });
@endphp

<div class="space-y-6">
    <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm lg:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-violet-500">Bộ phận đổi / trả</p>
            </div>
            <a href="{{ route('admin.orders') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:border-[#bc9c75] hover:text-[#8c6c41] transition">
                <i class="fa-solid fa-cart-shopping"></i>
                Về danh sách đơn
            </a>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded-2xl border border-violet-100 bg-violet-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-violet-500">Tổng yêu cầu</p>
                <h3 class="mt-2 text-2xl font-black text-violet-700">{{ number_format($summary['total']) }}</h3>
                <p class="mt-1 text-xs text-violet-600">Tất cả yêu cầu đang có</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-amber-500">Chờ xử lý</p>
                <h3 class="mt-2 text-2xl font-black text-amber-700">{{ number_format($summary['pending']) }}</h3>
                <p class="mt-1 text-xs text-amber-600">Cần xem ảnh và xác minh</p>
            </div>
            <div class="rounded-2xl border border-sky-100 bg-sky-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-sky-500">Đã duyệt</p>
                <h3 class="mt-2 text-2xl font-black text-sky-700">{{ number_format($summary['approved']) }}</h3>
                <p class="mt-1 text-xs text-sky-600">Đang chờ hoàn tất</p>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-rose-500">Đã từ chối</p>
                <h3 class="mt-2 text-2xl font-black text-rose-700">{{ number_format($summary['rejected']) }}</h3>
                <p class="mt-1 text-xs text-rose-600">Có lý do rõ ràng</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-emerald-500">Đã hoàn tất</p>
                <h3 class="mt-2 text-2xl font-black text-emerald-700">{{ number_format($summary['completed']) }}</h3>
                <p class="mt-1 text-xs text-emerald-600">Đã cập nhật xong</p>
            </div>
            <div class="rounded-2xl border border-orange-100 bg-orange-50/60 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-orange-500">Hôm nay</p>
                <h3 class="mt-2 text-2xl font-black text-orange-700">{{ number_format($summary['today']) }}</h3>
                <p class="mt-1 text-xs text-orange-600">Yêu cầu mới trong ngày</p>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-gray-100 bg-white p-4 shadow-sm lg:p-6">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.12em] text-gray-600">Bộ lọc đổi / trả</h3>
                <p class="mt-1 text-xs text-gray-400">Gõ để lọc tự động theo mã đơn, khách hàng, lý do hoặc ghi chú.</p>
            </div>
            <button type="button" wire:click="resetFilters"
                class="text-xs font-bold text-[#bc9c75] hover:text-[#9c7747] transition">Xóa bộ lọc</button>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:grid-cols-4">
            <input type="text" wire:model.live.debounce.350ms="q" autocomplete="off"
                placeholder="Mã đơn / khách hàng / lý do / ghi chú"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">

            <select wire:model.live="status"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">
                <option value="">Tất cả trạng thái</option>
                @foreach ($statusOptions as $item)
                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="type"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">
                <option value="">Tất cả loại</option>
                @foreach ($typeOptions as $item)
                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                @endforeach
            </select>

            <div class="flex items-center justify-end">
                <span wire:loading wire:target="q,status,type"
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
                    Trạng thái: {{ $statusLabel }}
                </span>
            @endif
            @if ($typeLabel)
                <span
                    class="inline-flex items-center rounded-full bg-violet-100 px-3 py-1 text-[11px] font-semibold text-violet-700">
                    Loại: {{ $typeLabel }}
                </span>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 lg:px-6">
            <h3 class="text-sm font-black uppercase tracking-[0.12em] text-gray-600">Danh sách yêu cầu đổi / trả</h3>
            <p class="text-xs text-gray-400">Hiển thị {{ $returnRequests->count() }} /
                {{ number_format($returnRequests->total()) }} yêu cầu</p>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse ($returnRequests as $returnRequest)
                @php
                    $requestTypeKey = $returnRequest->request_type?->value ?? (string) $returnRequest->request_type;
                    $requestStatusKey = $returnRequest->status?->value ?? (string) $returnRequest->status;
                    $requestType = $typeMap[$requestTypeKey] ?? [
                        'label' => ucfirst($requestTypeKey),
                        'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                    ];
                    $requestStatus = $statusMap[$requestStatusKey] ?? [
                        'label' => ucfirst($requestStatusKey),
                        'class' => 'bg-slate-100 text-slate-700 border-slate-200',
                    ];
                    $order = $returnRequest->order;
                    $isCompleted =
                        ($returnRequest->status?->value ?? (string) $returnRequest->status) ===
                        OrderReturnRequestStatus::COMPLETED->value;
                    $isRejected =
                        ($returnRequest->status?->value ?? (string) $returnRequest->status) ===
                        OrderReturnRequestStatus::REJECTED->value;
                @endphp

                <div wire:key="return-request-{{ $returnRequest->id }}"
                    class="grid grid-cols-1 gap-4 px-4 py-5 lg:grid-cols-[1.2fr_1fr_1fr] lg:px-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-black text-gray-800">{{ $order?->order_code ?? 'N/A' }}</span>
                            <span
                                class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $requestType['class'] }}">{{ $requestType['label'] }}</span>
                            <span
                                class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $requestStatus['class'] }}">{{ $requestStatus['label'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500">
                            Khách: <span
                                class="font-semibold text-gray-700">{{ $order?->user?->full_name ?? ($order?->guest_name ?? ($returnRequest->user?->full_name ?? 'N/A')) }}</span>
                            · SĐT: <span
                                class="font-semibold text-gray-700">{{ $order?->user?->phone_number ?? ($order?->guest_phone ?? ($returnRequest->user?->phone_number ?? 'N/A')) }}</span>
                        </p>
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold text-gray-900">Lý do:</span> {{ $returnRequest->reason }}
                        </p>
                        @if ($returnRequest->details)
                            <p class="text-sm leading-6 text-gray-600">{{ $returnRequest->details }}</p>
                        @endif
                        <div class="flex flex-wrap gap-2 text-xs">
                            <span
                                class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-600">
                                Đơn:
                                {{ OrderStatus::tryFrom((string) $order?->status)?->label() ?? ucfirst((string) $order?->status) }}
                            </span>
                            <span
                                class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-600">
                                Giao:
                                {{ ShippingStatus::tryFrom((string) $order?->shipping_status)?->label() ?? ucfirst((string) $order?->shipping_status) }}
                            </span>
                            <span
                                class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-600">
                                Thanh toán:
                                {{ $order?->payment?->status ? PaymentStatus::tryFrom((string) $order->payment->status)?->label() ?? ucfirst((string) $order->payment->status) : 'Chưa tạo giao dịch' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400">Tạo lúc {{ $returnRequest->created_at?->format('d/m/Y H:i') }}
                            @if ($returnRequest->resolved_at)
                                · Xử lý lúc {{ $returnRequest->resolved_at?->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3 text-xs">
                            <p class="font-black uppercase tracking-[0.12em] text-gray-500">Ảnh minh chứng</p>
                            @if (!empty($returnRequest->evidence_images))
                                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
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
                                            class="group block overflow-hidden rounded-2xl border border-white bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                            <img src="{{ $imageUrl }}" alt="Ảnh minh chứng"
                                                class="aspect-square h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-2 text-gray-500">Chưa có ảnh minh chứng.</p>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if ($returnRequest->admin_note)
                            <div class="rounded-2xl border border-violet-100 bg-violet-50 p-3">
                                <p class="text-[10px] font-black uppercase tracking-[0.12em] text-violet-500">Ghi chú
                                    admin</p>
                                <p class="mt-2 text-sm text-violet-900">{{ $returnRequest->admin_note }}</p>
                            </div>
                        @endif

                        @if ($isCompleted || $isRejected)
                            <div
                                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                                Yêu cầu đã hoàn tất và không thể chỉnh sửa thêm.
                            </div>
                        @else
                            <form action="{{ route('admin.orders.return-request.update', $returnRequest) }}"
                                method="POST" class="space-y-2 rounded-2xl border border-gray-100 bg-white p-3">
                                @csrf
                                @method('PUT')

                                <select name="status"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">
                                    @foreach ($statusOptions as $statusItem)
                                        <option value="{{ $statusItem->value }}" @selected($requestStatusKey === $statusItem->value)>
                                            {{ $statusItem->label() }}
                                        </option>
                                    @endforeach
                                </select>

                                <textarea name="admin_note" rows="3" placeholder="Ghi chú xử lý, xác nhận, hoặc lý do từ chối"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10">{{ old('admin_note', $returnRequest->admin_note ?? '') }}</textarea>

                                <button type="submit"
                                    class="w-full rounded-xl bg-[#bc9c75] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#a88966] transition">
                                    Cập nhật đổi / trả
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center text-gray-500">
                    Không có yêu cầu đổi / trả nào phù hợp.
                </div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 px-4 py-3">
            {{ $returnRequests->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
