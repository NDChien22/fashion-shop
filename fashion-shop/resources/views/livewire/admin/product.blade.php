<div>
    @if (session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div class="flex gap-4 flex-1">
            <div class="relative w-full max-w-[50%]">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Tìm theo tên hoặc mã sản phẩm..."
                    class="w-full bg-white border border-gray-100 rounded-xl py-2.5 pl-10 pr-4 text-xs focus:ring-1 focus:ring-[#bc9c75] outline-none shadow-sm">
            </div>



            <select wire:model.live="categoryId"
                class="bg-white border border-gray-100 rounded-xl px-4 py-2 text-xs shadow-sm outline-none w-40 text-gray-600">
                <option value="">Danh mục</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="collectionId"
                class="bg-white border border-gray-100 rounded-xl px-4 py-2 text-xs shadow-sm outline-none w-40 text-gray-600">
                <option value="">Bộ sưu tập</option>
                @foreach ($collections as $collection)
                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('admin.add-product') }}"
            class="px-5 py-2.5 bg-[#bc9c75] text-white rounded-xl text-xs font-bold shadow-md shadow-[#bc9c75]/20 hover:opacity-90 transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus text-[10px]"></i> Thêm sản phẩm
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left text-[12px]">
            <thead
                class="bg-[#fcfaf8] border-b border-gray-50 text-gray-400 font-bold uppercase tracking-wider text-[10px]">
                <tr>
                    <th class="px-6 py-4">Sản phẩm</th>
                    <th class="px-6 py-4 text-center">Danh mục</th>
                    <th class="px-6 py-4 text-center">Giá bán</th>
                    <th class="px-6 py-4 text-center">Tồn kho</th>
                    <th class="px-6 py-4 text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-gray-600">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50/50 transition cursor-pointer"
                        wire:click="showProductDetail({{ $product->id }})">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div>
                                    <img src="{{ $product->main_image_url ? asset($product->main_image_url) : '/asset/img/product-1.jpg' }}"
                                        class="w-10 h-10 rounded-lg object-cover">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $product->name }}</p>
                                    <p class="text-[10px] text-gray-400">ID: #{{ $product->product_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">{{ $product->category?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center font-semibold">
                            {{ number_format((float) $product->base_price, 0, ',', '.') }}đ</td>
                        <td class="px-6 py-4 text-center">{{ (int) ($product->total_stock ?? 0) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if ($product->is_active)
                                <span
                                    class="bg-green-50 text-green-500 px-2 py-1 rounded-full font-bold text-[10px]">Đang
                                    bán</span>
                            @else
                                <span class="bg-red-50 text-red-500 px-2 py-1 rounded-full font-bold text-[10px]">Ngừng
                                    bán</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center space-x-2" wire:click.stop>
                            <a href="{{ route('admin.edit-product', $product->slug) }}" wire:click.stop
                                class="text-gray-400 hover:text-[#bc9c75] inline-flex">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <button type="button" wire:click.stop="deleteProduct({{ $product->id }})"
                                wire:confirm="Bạn có chắc muốn xóa sản phẩm này không?"
                                class="text-gray-400 hover:text-red-500 inline-flex">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                            Chưa có sản phẩm nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-50">
            {{ $products->links() }}
        </div>
    </div>

    @if ($showProductDetailModal && $selectedProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            wire:click.self="closeProductDetailModal">
            <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden relative">
                <button wire:click="closeProductDetailModal"
                    class="absolute top-5 right-5 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 shadow-sm flex items-center justify-center z-10 transition-all">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-8 bg-[#fcfaf8]">
                        <div
                            class="relative aspect-3/4 rounded-2xl overflow-hidden border border-gray-100 bg-white mb-4 shadow-sm">
                            <img src="{{ $selectedDetailImage ?: $selectedProduct['main_image'] }}"
                                class="w-full h-full object-cover" alt="{{ $selectedProduct['name'] }}">

                            @if (count($selectedProduct['preview_images']) > 1)
                                <button type="button" wire:click="showPreviousDetailImage"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/45 text-white hover:bg-black/60 transition">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                                <button type="button" wire:click="showNextDetailImage"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/45 text-white hover:bg-black/60 transition">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                            @endif
                        </div>

                        <div class="flex gap-2 overflow-x-auto">
                            @foreach ($selectedProduct['preview_images'] as $index => $image)
                                <button type="button" wire:click="setDetailImage({{ $index }})"
                                    class="shrink-0 rounded-lg overflow-hidden border-2 {{ ($selectedDetailImage ?: $selectedProduct['main_image']) === $image ? 'border-[#bc9c75]' : 'border-gray-200' }}">
                                    <img src="{{ $image }}"
                                        class="w-16 h-16 object-cover {{ ($selectedDetailImage ?: $selectedProduct['main_image']) === $image ? 'opacity-100' : 'opacity-60 hover:opacity-90' }}"
                                        alt="{{ $selectedProduct['name'] }}">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-8 max-h-[85vh] overflow-y-auto space-y-5">
                        <div class="border-b border-gray-50 pb-4">
                            <span class="text-[#bc9c75] font-bold text-[10px] uppercase tracking-widest">Thông tin chi
                                tiết</span>
                            <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $selectedProduct['name'] }}</h2>
                            <p class="text-xs text-gray-400 font-medium tracking-tighter">Mã sản phẩm:
                                {{ $selectedProduct['product_code'] }}</p>
                        </div>

                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mô tả sản
                                phẩm</h4>
                            <p
                                class="text-xs text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                                {{ $selectedProduct['description'] ?: 'Chưa có mô tả sản phẩm.' }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Phân loại &
                                Giá</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 bg-gray-50 rounded-xl">
                                    <p class="text-[10px] text-gray-400">Danh mục</p>
                                    <p class="font-bold text-gray-800">{{ $selectedProduct['category'] ?: '-' }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-xl">
                                    <p class="text-[10px] text-gray-400">Giá bán</p>
                                    <p class="font-bold text-[#bc9c75]">
                                        {{ number_format($selectedProduct['base_price'], 0, ',', '.') }}đ
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Bộ sưu tập
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="px-3 py-1 bg-[#bc9c75]/10 text-[#bc9c75] text-[10px] font-bold rounded-full border border-[#bc9c75]/20">
                                    {{ $selectedProduct['collection'] ?: 'Chưa có bộ sưu tập' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kích thước &
                                Màu sắc sẵn có</h4>
                            <div class="border border-gray-100 rounded-xl overflow-hidden">
                                <table class="w-full text-[11px]">
                                    <thead class="bg-gray-50 text-gray-500">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Size</th>
                                            <th class="px-4 py-2 text-left">Màu sắc</th>
                                            <th class="px-4 py-2 text-right">Tồn kho</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse ($selectedProduct['skus'] as $sku)
                                            <tr>
                                                <td class="px-4 py-2 font-bold italic">{{ $sku['size'] ?: '-' }}</td>
                                                <td class="px-4 py-2 text-gray-600">{{ $sku['color'] ?: '-' }}</td>
                                                <td class="px-4 py-2 text-right font-bold text-gray-800">
                                                    {{ str_pad((string) $sku['stock'], 2, '0', STR_PAD_LEFT) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-center text-gray-400">Chưa có
                                                    biến thể.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t border-gray-50">
                            <div class="flex items-center gap-2">
                                @if ($selectedProduct['is_active'])
                                    <span
                                        class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                                    <span class="text-[11px] font-bold text-gray-700">Đang hiển thị trên Web</span>
                                @else
                                    <span
                                        class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                                    <span class="text-[11px] font-bold text-gray-700">Ngừng hiển thị</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-gray-400">
                                Cập nhật cuối: {{ $selectedProduct['updated_at'] ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
