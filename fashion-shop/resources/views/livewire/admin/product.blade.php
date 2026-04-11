<div>
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
                            <button type="button"
                                onclick="ffConfirmLivewireDelete(this, 'deleteProduct', {{ $product->id }}, 'Bạn có chắc muốn xóa sản phẩm này không?')"
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
        <x-admin.product-detail-modal id="livewireProductDetailModal" :show="$showProductDetailModal"
            closeAction="closeProductDetailModal" maxWidth="max-w-4xl" contentClass="grid grid-cols-1 md:grid-cols-2">
            <x-admin.product-detail-content mode="livewire" :product="$selectedProduct" :selectedDetailImage="$selectedDetailImage" />
        </x-admin.product-detail-modal>
    @endif
</div>
