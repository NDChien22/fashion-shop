<div class="basis-full order-4 lg:order-0 lg:basis-auto lg:grow lg:max-w-2xl lg:mx-8"
    wire:click.away="$set('showResults', false)">
    <div class="hidden lg:block relative w-full">
        <form wire:submit.prevent="submitSearch" class="relative w-full">
            <input type="text" wire:model.live.debounce.300ms="search" wire:keydown.escape="$set('showResults', false)"
                placeholder="Tìm kiếm sản phẩm..."
                class="w-full bg-gray-100 border border-transparent focus:border-[#bc9c75] focus:bg-white rounded-full py-1.5 pl-4 pr-10 outline-none transition-all text-sm">
            <button type="submit" class="absolute right-0 top-0 h-full px-3 text-gray-500 hover:text-[#bc9c75]">
                <i class="ri-search-line"></i>
            </button>
        </form>

        @if ($showResults && $products->isNotEmpty())
            <div
                class="absolute left-0 right-0 mt-2 rounded-2xl border border-gray-100 bg-white shadow-2xl overflow-hidden z-50">
                <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Kết quả sản
                        phẩm</span>
                    <button type="button" wire:click="$set('showResults', false)"
                        class="text-xs text-gray-400 hover:text-gray-700">Đóng</button>
                </div>

                <div class="max-h-96 overflow-auto divide-y divide-gray-100">
                    @foreach ($products as $product)
                        @php
                            $imageUrl = $product->main_image_url
                                ? (\Illuminate\Support\Str::startsWith($product->main_image_url, [
                                    'http://',
                                    'https://',
                                    '/',
                                ])
                                    ? $product->main_image_url
                                    : '/' . ltrim($product->main_image_url, '/'))
                                : '/asset/img/product-1.jpg';
                        @endphp

                        <button type="button" wire:click="selectSuggestion(@js($product->name))"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 transition flex items-center gap-3">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                class="w-12 h-12 rounded-xl object-cover bg-gray-100 shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $product->name }}</p>
                                <p class="text-[11px] text-gray-500 truncate">
                                    {{ $product->product_code }}
                                    @if ($product->category?->name)
                                        • {{ $product->category->name }}
                                    @endif
                                    @if ($product->collection?->name)
                                        • {{ $product->collection->name }}
                                    @endif
                                </p>
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="p-3 bg-gray-50 border-t border-gray-100">
                    <button type="button" wire:click="submitSearch"
                        class="w-full rounded-xl bg-[#bc9c75] px-4 py-2 text-xs font-bold uppercase tracking-widest text-white hover:brightness-110 transition">
                        Xem tất cả kết quả
                    </button>
                </div>
            </div>
        @elseif ($showResults && mb_strlen(trim($search)) >= 2)
            <div
                class="absolute left-0 right-0 mt-2 rounded-2xl border border-gray-100 bg-white shadow-2xl overflow-hidden z-50 p-4">
                <p class="text-sm font-semibold text-gray-700">Không tìm thấy sản phẩm phù hợp.</p>
                <p class="mt-1 text-xs text-gray-400">Thử nhập tên sản phẩm hoặc mã sản phẩm khác.</p>
            </div>
        @endif
    </div>

    <div class="lg:hidden w-full px-4 pb-2 pt-1 bg-white border-b border-gray-100">
        <div class="relative w-full">
            <form wire:submit.prevent="submitSearch" class="relative w-full">
                <input type="text" wire:model.live.debounce.300ms="search"
                    wire:keydown.escape="$set('showResults', false)" placeholder="Tìm kiếm sản phẩm..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2.5 pl-4 pr-10 text-sm outline-none focus:ring-1 focus:ring-[#bc9c75] focus:border-[#bc9c75]">
                <button type="submit" class="absolute right-0 top-0 h-full px-3 text-gray-400 hover:text-[#bc9c75]">
                    <i class="ri-search-line"></i>
                </button>
            </form>

            @if ($showResults && $products->isNotEmpty())
                <div
                    class="absolute left-0 right-0 mt-2 rounded-2xl border border-gray-100 bg-white shadow-2xl overflow-hidden z-50">
                    <div class="max-h-80 overflow-auto divide-y divide-gray-100">
                        @foreach ($products as $product)
                            <button type="button" wire:click="selectSuggestion(@js($product->name))"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 transition flex items-center gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $product->name }}</p>
                                    <p class="text-[11px] text-gray-500 truncate">{{ $product->product_code }}</p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @elseif ($showResults && mb_strlen(trim($search)) >= 2)
                <div
                    class="absolute left-0 right-0 mt-2 rounded-2xl border border-gray-100 bg-white shadow-2xl overflow-hidden z-50 p-4">
                    <p class="text-sm font-semibold text-gray-700">Không tìm thấy sản phẩm phù hợp.</p>
                    <p class="mt-1 text-xs text-gray-400">Thử nhập tên sản phẩm hoặc mã sản phẩm khác.</p>
                </div>
            @endif
        </div>
    </div>
</div>
