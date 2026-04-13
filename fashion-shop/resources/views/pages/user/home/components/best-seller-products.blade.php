@php
    $bestSellerItems = collect($bestSellerProducts ?? []);
    $fallbackImage = 'https://placehold.co/600x800/f3f4f6/9ca3af?text=Product';

    $resolveProductImage = function ($path) {
        if (!is_string($path) || trim($path) === '') {
            return $fallbackImage;
        }

        $normalizedPath = str_replace('\\', '/', trim($path));

        if (\Illuminate\Support\Str::startsWith($normalizedPath, ['http://', 'https://'])) {
            return $normalizedPath;
        }

        if (
            \Illuminate\Support\Str::startsWith($normalizedPath, [
                '/storage/',
                'storage/',
                '/uploads/',
                'uploads/',
                '/images/',
                'images/',
            ])
        ) {
            return asset(ltrim($normalizedPath, '/'));
        }

        return asset('storage/' . ltrim($normalizedPath, '/'));
    };
@endphp

@if ($bestSellerItems->isNotEmpty())
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-wider">Sản phẩm bán chạy</h2>
                <div class="h-1 w-20 bg-[#bc9c75] mt-2"></div>
            </div>
            <a href="{{ route('user.product') }}" class="text-[#bc9c75] font-medium hover:underline transition-all">
                Xem tất cả <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-6">
            @foreach ($bestSellerItems as $product)
                <a href="{{ route('user.product') }}" class="block group">
                    <div class="rounded-2xl bg-white overflow-hidden border border-gray-100 shadow-sm">
                        <div class="aspect-3/4 overflow-hidden">
                            <img src="{{ $resolveProductImage($product->main_image_url) }}" alt="{{ $product->name }}"
                                onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="p-3">
                            @php
                                $basePrice = (float) $product->base_price;
                                $salePrice = isset($product->sale_price) ? (float) $product->sale_price : null;
                                $isOnSale = is_numeric($salePrice) && $salePrice < $basePrice;
                            @endphp
                            <h4 class="text-sm font-semibold text-gray-800 line-clamp-2 min-h-10">{{ $product->name }}
                            </h4>
                            <div class="mt-2 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#bc9c75] font-black text-base">
                                        {{ number_format($isOnSale ? $salePrice : $basePrice, 0, ',', '.') }}đ
                                    </span>
                                    @if ($isOnSale)
                                        <span class="text-xs text-gray-400 line-through">
                                            {{ number_format($basePrice, 0, ',', '.') }}đ
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">Đã bán
                                    {{ number_format((int) ($product->sold_qty ?? 0)) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
