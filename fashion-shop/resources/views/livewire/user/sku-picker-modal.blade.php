<div>
    @if ($isOpen && $product)
        <div class="fixed inset-0 z-70 flex items-center justify-center p-4" wire:keydown.escape.window="closeModal">
            <button type="button" wire:click="closeModal" class="absolute inset-0 bg-black/55"></button>

            <div
                class="relative w-full max-w-3xl rounded-3xl border border-[#e9dfcf] bg-white shadow-[0_24px_64px_rgba(15,23,42,0.24)] overflow-hidden">
                <div class="flex items-center justify-between border-b border-[#f0e8db] px-5 py-4 md:px-6 md:py-4.5">
                    <h3 class="text-base md:text-xl font-black tracking-wider text-[#1f3446] uppercase">Thông tin sản
                        phẩm</h3>
                    <button type="button" wire:click="closeModal"
                        class="h-9 w-9 rounded-full border border-gray-200 text-gray-500 hover:text-[#1f3446] hover:border-[#c7ad84] transition">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <div class="px-5 py-5 md:px-6 md:py-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-[150px_minmax(0,1fr)] gap-4 md:gap-5 items-start">
                        <div
                            class="aspect-3/4 rounded-2xl overflow-hidden bg-[#f6f2eb] border border-[#efe5d8] max-w-37.5 w-full">
                            <img src="{{ $this->productImageUrl }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">
                        </div>

                        <div class="space-y-2.5">
                            <h4 class="text-xl md:text-2xl font-black leading-tight text-[#1f3446] line-clamp-2">
                                {{ $product->name }}</h4>

                            <div class="flex items-center gap-2 text-[#5e738a] text-sm md:text-base">
                                <span class="font-semibold">Mã:</span>
                                <span class="font-bold">{{ $this->selectedSkuLabel }}</span>
                            </div>

                            <div class="flex flex-wrap items-end justify-between gap-2.5 pt-1">
                                <div class="flex items-end gap-3 flex-wrap">
                                    <div
                                        class="text-2xl md:text-3xl font-black leading-none {{ $this->hasSalePrice ? 'text-[#c7362f]' : 'text-[#1f3446]' }}">
                                        {{ number_format($this->displayPrice, 0, ',', '.') }} đ
                                    </div>
                                    @if ($this->hasSalePrice)
                                        <div
                                            class="text-sm md:text-base font-semibold leading-none text-gray-400 line-through">
                                            {{ number_format($basePrice, 0, ',', '.') }} đ
                                        </div>
                                    @endif
                                </div>
                                <a wire:navigate
                                    href="{{ route('user.product-detail', ['product' => $product->slug ?: $product->id]) }}"
                                    class="inline-flex items-center gap-1.5 text-[#1f3446] text-sm md:text-base font-bold hover:text-[#c7362f] transition-colors">
                                    Chi tiết
                                    <i class="ri-arrow-right-double-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-[#1f3446]">
                            <span class="text-base md:text-lg">Màu sắc:</span>
                            <span
                                class="text-base md:text-lg font-semibold">{{ $selectedColor !== '' ? $selectedColor : 'Chưa chọn' }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2.5">
                            @forelse ($colorOptions as $color)
                                <label class="cursor-pointer">
                                    <input type="radio" name="sku_color" value="{{ $color }}"
                                        wire:model.live="selectedColor" class="sr-only">
                                    <span
                                        class="inline-flex px-3.5 py-2 rounded-xl border text-sm font-semibold transition {{ $selectedColor === $color ? 'border-[#1f3446] bg-[#1f3446] text-white shadow-sm' : 'border-gray-300 text-gray-700 hover:border-[#1f3446]' }}">
                                        {{ $color }}
                                    </span>
                                </label>
                            @empty
                                <span class="text-sm text-gray-500">Không có tùy chọn màu.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-[#1f3446]">
                            <span class="text-base md:text-lg">Kích cỡ:</span>
                            <span
                                class="inline-flex items-center rounded-md bg-[#2f4253] text-white text-xs md:text-sm px-2.5 py-1 font-semibold">
                                {{ $selectedSize !== '' ? $selectedSize : 'Chưa chọn' }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2.5">
                            @forelse ($sizeOptions as $size)
                                @php
                                    $hasStockForSize = collect($skus)->contains(function ($sku) use (
                                        $size,
                                        $selectedColor,
                                    ) {
                                        return ($sku['size'] ?? '') === $size &&
                                            ($sku['stock'] ?? 0) > 0 &&
                                            ($selectedColor === '' || ($sku['color'] ?? '') === $selectedColor);
                                    });
                                @endphp
                                <label class="{{ $hasStockForSize ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                                    <input type="radio" name="sku_size" value="{{ $size }}"
                                        wire:model.live="selectedSize" @disabled(!$hasStockForSize) class="sr-only">
                                    <span
                                        class="inline-flex min-w-12 justify-center px-3 py-2 rounded-xl border text-sm md:text-base font-semibold transition {{ $selectedSize === $size ? 'border-[#1f3446] text-[#1f3446] bg-[#eef3f7] shadow-sm' : 'border-gray-300 text-gray-700' }} {{ $hasStockForSize ? 'hover:border-[#1f3446]' : 'opacity-40 cursor-not-allowed' }}">
                                        {{ $size }}
                                    </span>
                                </label>
                            @empty
                                <span class="text-sm text-gray-500">Không có tùy chọn kích cỡ.</span>
                            @endforelse
                        </div>
                    </div>

                    <p class="text-sm {{ $selectedSkuId ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $statusMessage }}</p>
                </div>

                <div class="border-t border-[#f0e8db] px-5 md:px-6 py-4 bg-[#fcfaf7]">
                    <button type="button" wire:click="addToCart" @disabled(!$selectedSkuId)
                        class="w-full h-11 rounded-xl bg-[#1f3446] text-white text-sm font-bold uppercase tracking-wider transition hover:bg-[#2d4d66] disabled:opacity-40 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="addToCart">Thêm vào giỏ hàng</span>
                        <span wire:loading wire:target="addToCart">Đang thêm...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
