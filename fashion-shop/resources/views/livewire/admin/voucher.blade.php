<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black">Tổng voucher</p>
            <div class="mt-2 flex items-end justify-between">
                <p class="text-2xl font-black text-gray-800">{{ number_format($stats['total']) }}</p>
                <span class="text-xs text-[#bc9c75] font-bold">Hệ thống</span>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black">Đang hoạt động</p>
            <div class="mt-2 flex items-end justify-between">
                <p class="text-2xl font-black text-gray-800">{{ number_format($stats['active']) }}</p>
                <span class="text-xs text-green-600 font-bold">Active</span>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black">Sắp hết hạn</p>
            <div class="mt-2 flex items-end justify-between">
                <p class="text-2xl font-black text-gray-800">{{ number_format($stats['expiringSoon']) }}</p>
                <span class="text-xs text-amber-600 font-bold">7 ngày tới</span>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black">Lượt đã dùng</p>
            <div class="mt-2 flex items-end justify-between">
                <p class="text-2xl font-black text-gray-800">{{ number_format($stats['used']) }}</p>
                <span class="text-xs text-sky-600 font-bold">Used</span>
            </div>
        </div>
    </div>

    <div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
            <div class="relative w-full sm:w-80">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                <input type="text" wire:model.live.debounce.400ms="search"
                    placeholder="Tìm theo code, loại giảm, phạm vi..."
                    class="w-full bg-white border border-gray-100 rounded-xl py-2.5 pl-9 pr-4 text-xs focus:ring-1 focus:ring-[#bc9c75] outline-none shadow-sm">
            </div>

            <select wire:model.live="statusFilter"
                class="w-full sm:w-44 bg-white border border-gray-100 rounded-xl py-2.5 px-3 text-xs font-semibold text-gray-600 focus:ring-1 focus:ring-[#bc9c75] outline-none shadow-sm">
                <option value="all">Tất cả trạng thái</option>
                <option value="active">Đang hoạt động</option>
                <option value="inactive">Tạm dừng</option>
                <option value="expired">Hết hạn</option>
            </select>

            <select wire:model.live="scopeFilter"
                class="w-full sm:w-48 bg-white border border-gray-100 rounded-xl py-2.5 px-3 text-xs font-semibold text-gray-600 focus:ring-1 focus:ring-[#bc9c75] outline-none shadow-sm">
                <option value="all">Tất cả phạm vi</option>
                <option value="all_products">Toàn bộ sản phẩm</option>
                <option value="category">Theo danh mục</option>
                <option value="collection">Theo bộ sưu tập</option>
                <option value="product">Theo sản phẩm</option>
            </select>
        </div>

        <a href="{{ route('admin.add-voucher') }}"
            class="px-4 py-2.5 bg-[#bc9c75] text-white rounded-xl text-xs font-bold shadow-sm hover:opacity-90 transition inline-flex items-center">
            <i class="fa-solid fa-plus mr-2"></i> Tạo mã mới
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($vouchers as $voucher)
            @php
                $isExpired = $voucher->end_date && \Carbon\Carbon::parse($voucher->end_date)->lt(now());
                $isUpcoming = $voucher->start_date && \Carbon\Carbon::parse($voucher->start_date)->gt(now());
                $isActiveWindow = !$isExpired && !$isUpcoming && $voucher->is_active;
                $usageLimit = (int) ($voucher->usage_limit ?? 0);
                $usedCount = (int) ($voucher->used_count ?? 0);
                $progress = $usageLimit > 0 ? min(100, (int) round(($usedCount / $usageLimit) * 100)) : 0;

                if ($voucher->category === 'all') {
                    $scopeText = 'Toàn bộ sản phẩm';
                } elseif ($voucher->category === 'category') {
                    $scopeName = $voucher->categoryDetail?->name;
                    $scopeText = $scopeName ? 'Danh mục: ' . $scopeName : 'Danh mục đã xóa';
                } elseif ($voucher->category === 'collection') {
                    $scopeName = $voucher->collectionDetail?->name;
                    $scopeText = $scopeName ? 'Bộ sưu tập: ' . $scopeName : 'Bộ sưu tập đã xóa';
                } else {
                    $productName = $voucher->productDetail?->name;
                    $productCode = $voucher->productDetail?->product_code;
                    $scopeText = $productName
                        ? 'Sản phẩm: ' . trim(($productCode ? $productCode . ' - ' : '') . $productName)
                        : 'Sản phẩm đã xóa';
                }

                if ($isExpired) {
                    $badgeClass = 'bg-red-50 text-red-600 border-red-100';
                    $badgeLabel = 'Hết hạn';
                } elseif ($isUpcoming) {
                    $badgeClass = 'bg-amber-50 text-amber-600 border-amber-100';
                    $badgeLabel = 'Sắp diễn ra';
                } elseif ($isActiveWindow) {
                    $badgeClass = 'bg-green-50 text-green-600 border-green-100';
                    $badgeLabel = 'Hoạt động';
                } else {
                    $badgeClass = 'bg-slate-50 text-slate-500 border-slate-100';
                    $badgeLabel = 'Tạm dừng';
                }
            @endphp

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-50 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-16 h-16 bg-[#bc9c75]/10 rounded-bl-full flex items-center justify-center">
                    <i class="fa-solid fa-ticket text-[#bc9c75]"></i>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-black text-[#bc9c75] uppercase tracking-wider">CODE:
                        {{ $voucher->code }}</span>
                    <h3 class="text-base font-bold text-gray-800">
                        @if ($voucher->discount_type === 'percent')
                            Giảm
                            {{ rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') }}%
                        @elseif ($voucher->discount_type === 'shipping')
                            Giảm phí vận chuyển {{ number_format((float) $voucher->discount_value, 0, ',', '.') }}đ
                        @else
                            Giảm {{ number_format((float) $voucher->discount_value, 0, ',', '.') }}đ
                        @endif
                    </h3>
                    <p class="text-[11px] text-gray-400">
                        Đơn tối thiểu: {{ number_format((float) $voucher->min_order_value, 0, ',', '.') }}đ
                    </p>
                    <p class="text-[11px] text-gray-400">{{ $scopeText }}</p>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-50 space-y-3">
                    <div class="flex justify-between text-[10px] gap-4">
                        <span class="text-gray-400 uppercase font-bold tracking-tighter truncate">
                            Tiến độ:
                            {{ number_format($usedCount) }}/{{ $usageLimit > 0 ? number_format($usageLimit) : 'Không giới hạn' }}
                        </span>
                        <span class="text-gray-500 font-bold tracking-tighter">
                            Hết hạn:
                            {{ $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') : '--/--/----' }}
                        </span>
                    </div>

                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#bc9c75] h-full" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <span class="text-[9px] font-bold px-2 py-1 rounded-full border uppercase {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.edit-voucher', $voucher) }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-[#bc9c75]/10 hover:text-[#bc9c75] transition-all"
                            title="Chỉnh sửa">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>

                        <button type="button" wire:click="toggleStatus({{ $voucher->id }})"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-sky-50 hover:text-sky-500 transition-all"
                            title="Đổi trạng thái">
                            <i class="fa-solid fa-power-off"></i>
                        </button>

                        <button type="button"
                            onclick="ffConfirmLivewireDelete(this, 'deleteVoucher', {{ $voucher->id }}, 'Bạn có chắc muốn xóa voucher này?')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all"
                            title="Xóa">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="md:col-span-2 xl:col-span-3 bg-white border border-dashed border-gray-200 rounded-2xl p-10 text-center">
                <p class="text-sm font-semibold text-gray-600">Chưa có voucher phù hợp điều kiện lọc.</p>
                <p class="text-xs text-gray-400 mt-1">Thử thay đổi từ khóa tìm kiếm hoặc tạo voucher mới.</p>
            </div>
        @endforelse
    </div>

    @if ($vouchers->hasPages())
        <div class="pt-2">
            {{ $vouchers->links() }}
        </div>
    @endif

</div>
