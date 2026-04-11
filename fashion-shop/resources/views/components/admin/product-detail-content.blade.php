@props([
    'mode' => 'livewire',
    'product' => null,
    'selectedDetailImage' => null,
    'prefix' => 'productPreview',
    'showRemove' => true,
    'removeButtonText' => 'Xóa',
    'removeConfirmMessage' => 'Bạn có chắc muốn xóa sản phẩm này không?',
])

@if ($mode === 'livewire')
    <div class="p-8 bg-[#fcfaf8]">
        <div class="relative aspect-3/4 rounded-2xl overflow-hidden border border-gray-100 bg-white mb-4 shadow-sm">
            <img src="{{ $selectedDetailImage ?: $product['main_image'] ?? '' }}" class="w-full h-full object-cover"
                alt="{{ $product['name'] ?? 'Sản phẩm' }}">

            @if (!empty($product['preview_images']) && count($product['preview_images']) > 1)
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
            @foreach ($product['preview_images'] ?? [] as $index => $image)
                <button type="button" wire:click="setDetailImage({{ $index }})"
                    class="shrink-0 rounded-lg overflow-hidden border-2 {{ ($selectedDetailImage ?: $product['main_image'] ?? '') === $image ? 'border-[#bc9c75]' : 'border-gray-200' }}">
                    <img src="{{ $image }}"
                        class="w-16 h-16 object-cover {{ ($selectedDetailImage ?: $product['main_image'] ?? '') === $image ? 'opacity-100' : 'opacity-60 hover:opacity-90' }}"
                        alt="{{ $product['name'] ?? 'Sản phẩm' }}">
                </button>
            @endforeach
        </div>
    </div>

    <div class="p-8 max-h-[85vh] overflow-y-auto space-y-5">
        <div class="border-b border-gray-50 pb-4">
            <span class="text-[#bc9c75] font-bold text-[10px] uppercase tracking-widest">Thông tin chi tiết</span>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ $product['name'] ?? '' }}</h2>
            <p class="text-xs text-gray-400 font-medium tracking-tighter">Mã sản phẩm:
                {{ $product['product_code'] ?? '-' }}</p>
        </div>

        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mô tả sản phẩm</h4>
            <p class="text-xs text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                {{ $product['description'] ?: 'Chưa có mô tả sản phẩm.' }}
            </p>
        </div>

        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Phân loại & Giá</h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[10px] text-gray-400">Danh mục</p>
                    <p class="font-bold text-gray-800">{{ $product['category'] ?: '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[10px] text-gray-400">Giá bán</p>
                    <p class="font-bold text-[#bc9c75]">
                        {{ number_format((float) ($product['base_price'] ?? 0), 0, ',', '.') }}đ</p>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Bộ sưu tập</h4>
            <div class="flex flex-wrap gap-2">
                <span
                    class="px-3 py-1 bg-[#bc9c75]/10 text-[#bc9c75] text-[10px] font-bold rounded-full border border-[#bc9c75]/20">
                    {{ $product['collection'] ?: 'Chưa có bộ sưu tập' }}
                </span>
            </div>
        </div>

        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kích thước & Màu sắc sẵn có
            </h4>
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
                        @forelse (($product['skus'] ?? []) as $sku)
                            <tr>
                                <td class="px-4 py-2 font-bold italic">{{ $sku['size'] ?: '-' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $sku['color'] ?: '-' }}</td>
                                <td class="px-4 py-2 text-right font-bold text-gray-800">
                                    {{ str_pad((string) ($sku['stock'] ?? 0), 2, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-center text-gray-400">Chưa có biến thể.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-50 flex-wrap">
            <div class="flex items-center gap-2">
                @if (!empty($product['is_active']))
                    <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                    <span class="text-[11px] font-bold text-gray-700">Đang hiển thị trên Web</span>
                @else
                    <span class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                    <span class="text-[11px] font-bold text-gray-700">Ngừng hiển thị</span>
                @endif
            </div>
            <div class="text-[11px] text-gray-400">Cập nhật cuối: {{ $product['updated_at'] ?: '-' }}</div>

            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('admin.edit-product', $product['slug'] ?? '') }}"
                    class="px-4 py-2 rounded-lg bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-100 transition-all inline-flex items-center gap-2">
                    <i class="fa-regular fa-pen-to-square"></i>
                    Chỉnh sửa
                </a>
                @if ($showRemove)
                    <button type="button"
                        onclick="if(confirm('{{ $removeConfirmMessage }}')) { $wire.deleteSelectedProduct(); }"
                        class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition-all inline-flex items-center gap-2">
                        <i class="fa-regular fa-trash-can"></i>
                        {{ $removeButtonText }}
                    </button>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="p-8 bg-[#fcfaf8]">
        <div
            class="relative aspect-3/4 rounded-2xl overflow-hidden border border-gray-100 bg-white mb-4 shadow-sm flex items-center justify-center">
            <img id="{{ $prefix }}Image" src="" class="hidden w-full h-full object-cover"
                alt="Ảnh sản phẩm">
            <div id="{{ $prefix }}Fallback" class="text-gray-300 text-center">
                <i class="fa-solid fa-image text-4xl"></i>
                <p class="text-xs mt-2">Không có ảnh</p>
            </div>

            <button type="button" id="{{ $prefix }}PrevBtn"
                class="hidden absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/45 text-white hover:bg-black/60 transition">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <button type="button" id="{{ $prefix }}NextBtn"
                class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/45 text-white hover:bg-black/60 transition">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>

        <div id="{{ $prefix }}Thumbs" class="flex gap-2 overflow-x-auto"></div>
    </div>

    <div class="p-8 max-h-[85vh] overflow-y-auto space-y-5">
        <div class="border-b border-gray-50 pb-4">
            <span class="text-[#bc9c75] font-bold text-[10px] uppercase tracking-widest">Thông tin chi tiết</span>
            <h2 id="{{ $prefix }}Name" class="text-2xl font-bold text-gray-800 mt-1"></h2>
            <p id="{{ $prefix }}Code" class="text-xs text-gray-400 font-medium tracking-tighter"></p>
        </div>

        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mô tả sản phẩm</h4>
            <p id="{{ $prefix }}Description"
                class="text-xs text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100"></p>
        </div>

        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Phân loại & Giá</h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[10px] text-gray-400">Danh mục</p>
                    <p id="{{ $prefix }}Category" class="font-bold text-gray-800"></p>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[10px] text-gray-400">Giá bán</p>
                    <p id="{{ $prefix }}Price" class="font-bold text-[#bc9c75]"></p>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Bộ sưu tập</h4>
            <div class="flex flex-wrap gap-2">
                <span id="{{ $prefix }}Collection"
                    class="px-3 py-1 bg-[#bc9c75]/10 text-[#bc9c75] text-[10px] font-bold rounded-full border border-[#bc9c75]/20"></span>
            </div>
        </div>

        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kích thước & Màu sắc sẵn có
            </h4>
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <table class="w-full text-[11px]">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Size</th>
                            <th class="px-4 py-2 text-left">Màu sắc</th>
                            <th class="px-4 py-2 text-right">Tồn kho</th>
                        </tr>
                    </thead>
                    <tbody id="{{ $prefix }}Skus" class="divide-y divide-gray-50"></tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-50 flex-wrap">
            <div class="flex items-center gap-2">
                <span id="{{ $prefix }}Dot" class="w-2 h-2 rounded-full"></span>
                <span id="{{ $prefix }}StatusLabel" class="text-[11px] font-bold text-gray-700"></span>
            </div>
            <div id="{{ $prefix }}Updated" class="text-[11px] text-gray-400"></div>

            <div class="ml-auto flex items-center gap-2">
                <a id="{{ $prefix }}EditLink" href="#"
                    class="px-4 py-2 rounded-lg bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-100 transition-all inline-flex items-center gap-2">
                    <i class="fa-regular fa-pen-to-square"></i>
                    Chỉnh sửa
                </a>

                @if ($showRemove)
                    <form id="{{ $prefix }}RemoveForm" method="POST" data-confirm-delete="1"
                        data-confirm-message="{{ $removeConfirmMessage }}">
                        @csrf
                        <input type="hidden" name="product_id" id="{{ $prefix }}RemoveProductId"
                            value="">
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition-all inline-flex items-center gap-2 border-0 cursor-pointer">
                            <i class="fa-regular fa-trash-can"></i>
                            {{ $removeButtonText }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endif
