<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase leading-none mb-1">Tổng chiến dịch</p>
                <p class="text-lg font-bold">{{ number_format($stats['total']) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-[#ecfdf3] text-[#16a34a] rounded-xl flex items-center justify-center text-xl">
                <i class="fa-solid fa-play"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase leading-none mb-1">Đang chạy</p>
                <p class="text-lg font-bold">{{ number_format($stats['active']) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fa-solid fa-hourglass-start"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase leading-none mb-1">Sắp diễn ra</p>
                <p class="text-lg font-bold">{{ number_format($stats['upcoming']) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase leading-none mb-1">Đã hết hạn</p>
                <p class="text-lg font-bold">{{ number_format($stats['expired']) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-[#fffbf7] text-[#bc9c75] rounded-xl flex items-center justify-center text-xl">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase leading-none mb-1">Lượt đã bán</p>
                <p class="text-lg font-bold">{{ number_format($stats['used']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
            <div>
                <h3 class="font-bold text-gray-800">Danh sách chương trình</h3>
            </div>

            <a href="{{ route('admin.add-flash-sale') }}"
                class="bg-[#bc9c75] text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-sm hover:opacity-90 transition inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Tạo flash sale mới
            </a>
        </div>

        <div class="px-6 pt-5 flex flex-wrap gap-2">
            <button type="button" wire:click="setStatusFilter('all')"
                class="px-3 py-1 rounded-full text-[10px] font-bold border transition {{ $statusFilter === 'all' ? 'bg-gray-900 text-white border-gray-900' : 'bg-gray-100 text-gray-500 border-gray-100 hover:bg-gray-200' }}">
                Tất cả
            </button>
            <button type="button" wire:click="setStatusFilter('active')"
                class="px-3 py-1 rounded-full text-[10px] font-bold border transition {{ $statusFilter === 'active' ? 'bg-green-600 text-white border-green-600' : 'bg-green-50 text-green-600 border-green-100 hover:bg-green-100' }}">
                Đang chạy
            </button>
            <button type="button" wire:click="setStatusFilter('upcoming')"
                class="px-3 py-1 rounded-full text-[10px] font-bold border transition {{ $statusFilter === 'upcoming' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-600 border-amber-100 hover:bg-amber-100' }}">
                Sắp diễn ra
            </button>
            <button type="button" wire:click="setStatusFilter('paused')"
                class="px-3 py-1 rounded-full text-[10px] font-bold border transition {{ $statusFilter === 'paused' ? 'bg-slate-600 text-white border-slate-600' : 'bg-slate-50 text-slate-500 border-slate-100 hover:bg-slate-100' }}">
                Tạm dừng
            </button>
            <button type="button" wire:click="setStatusFilter('expired')"
                class="px-3 py-1 rounded-full text-[10px] font-bold border transition {{ $statusFilter === 'expired' ? 'bg-red-600 text-white border-red-600' : 'bg-red-50 text-red-600 border-red-100 hover:bg-red-100' }}">
                Hết hạn
            </button>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Chương trình</th>
                        <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Giảm giá</th>
                        <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Phạm vi</th>
                        <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Thời gian</th>
                        <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Trạng thái</th>
                        <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($flashSales as $flashSale)
                        @php
                            $isExpired = $flashSale->end_date && \Carbon\Carbon::parse($flashSale->end_date)->lt(now());
                            $isUpcoming =
                                $flashSale->start_date && \Carbon\Carbon::parse($flashSale->start_date)->gt(now());
                            $isActiveWindow = !$isExpired && !$isUpcoming && $flashSale->is_active;
                            $usageLimit = (int) ($flashSale->usage_limit ?? 0);
                            $usedCount = (int) ($flashSale->used_count ?? 0);
                            $progress = $usageLimit > 0 ? min(100, (int) round(($usedCount / $usageLimit) * 100)) : 0;

                            if ($flashSale->scope === 'all') {
                                $scopeText = 'Toàn bộ sản phẩm';
                            } elseif ($flashSale->scope === 'category') {
                                $scopeName = $flashSale->categoryDetail?->name;
                                $scopeText = $scopeName ? 'Danh mục: ' . $scopeName : 'Danh mục đã xóa';
                            } elseif ($flashSale->scope === 'collection') {
                                $scopeName = $flashSale->collectionDetail?->name;
                                $scopeText = $scopeName ? 'Bộ sưu tập: ' . $scopeName : 'Bộ sưu tập đã xóa';
                            } else {
                                $productName = $flashSale->productDetail?->name;
                                $productCode = $flashSale->productDetail?->product_code;
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

                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-tags"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-gray-800">{{ $flashSale->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-red-500">
                                    @if ($flashSale->discount_type === 'percent')
                                        -{{ rtrim(rtrim(number_format((float) $flashSale->discount_value, 2, '.', ''), '0'), '.') }}%
                                    @else
                                        -{{ number_format((float) $flashSale->discount_value, 0, ',', '.') }}đ
                                    @endif
                                </span>
                            </td>
                            <td class="p-4 text-[11px] leading-tight text-gray-600">
                                {{ $scopeText }}
                            </td>
                            <td class="p-4">
                                <div class="text-[11px] leading-tight">
                                    <p class="text-gray-600">Bắt đầu:
                                        {{ \Carbon\Carbon::parse($flashSale->start_date)->format('d/m/Y') }}</p>
                                    <p class="text-gray-400">Kết thúc:
                                        {{ \Carbon\Carbon::parse($flashSale->end_date)->format('d/m/Y') }}</p>
                                    <div class="mt-2 w-full bg-gray-100 h-1.5 rounded-full overflow-hidden max-w-40">
                                        <div class="bg-[#bc9c75] h-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span
                                    class="px-2 py-1 {{ $badgeClass }} rounded text-[10px] font-bold border uppercase">
                                    {{ $badgeLabel }}
                                </span>

                                <div class="mt-2">
                                    @if ($isExpired)
                                        <span class="text-[10px] text-gray-400 font-semibold">Không thể chỉnh trạng
                                            thái</span>
                                    @else
                                        <button type="button" wire:click="toggleStatus({{ $flashSale->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold border transition {{ $flashSale->is_active ? 'bg-sky-50 text-sky-600 border-sky-100 hover:bg-sky-100' : 'bg-slate-50 text-slate-500 border-slate-100 hover:bg-slate-100' }}">
                                            <i class="fa-solid fa-power-off"></i>
                                            {{ $flashSale->is_active ? 'Tắt' : 'Bật' }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.edit-flash-sale', $flashSale) }}"
                                    class="text-gray-400 hover:text-[#bc9c75] mx-1 inline-flex items-center"
                                    title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.delete-flash-sale', $flashSale) }}" method="POST"
                                    class="inline-block" data-confirm-delete="1"
                                    data-confirm-message="Bạn có chắc muốn xóa flash sale này?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 mx-1" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-sm text-gray-500">
                                Chưa có flash sale nào. Hãy tạo chiến dịch đầu tiên.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($flashSales->hasPages())
            <div class="px-6 pb-6">
                {{ $flashSales->links() }}
            </div>
        @endif
    </div>
</div>
