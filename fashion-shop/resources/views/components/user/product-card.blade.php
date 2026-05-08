@props([
    'product',
    'href' => null,
    'soldLabel' => null,
    'fallbackImage' => 'https://placehold.co/600x800/f3f4f6/9ca3af?text=Product',
    'removeOnUnwishlist' => false,
])

@php
    $productId = (int) ($product->id ?? 0);
    $name = (string) ($product->name ?? 'Sản phẩm');
    $imagePath = (string) ($product->main_image_url ?? '');

    if ($imagePath === '') {
        $imageUrl = $fallbackImage;
    } else {
        $normalizedPath = str_replace('\\', '/', trim($imagePath));

        if (\Illuminate\Support\Str::startsWith($normalizedPath, ['http://', 'https://'])) {
            $imageUrl = $normalizedPath;
        } elseif (
            \Illuminate\Support\Str::startsWith($normalizedPath, [
                '/storage/',
                'storage/',
                '/uploads/',
                'uploads/',
                '/images/',
                'images/',
            ])
        ) {
            $imageUrl = asset(ltrim($normalizedPath, '/'));
        } else {
            $imageUrl = asset('storage/' . ltrim($normalizedPath, '/'));
        }
    }

    $basePrice = (float) ($product->base_price ?? 0);
    $salePrice = isset($product->sale_price) ? (float) $product->sale_price : null;
    $isOnSale = is_numeric($salePrice) && $salePrice > 0 && $salePrice < $basePrice;
    $displayPrice = $isOnSale ? $salePrice : $basePrice;
    $isWhistlisted = in_array($productId, $globalWhistlistProductIds ?? [], true);
    $categoryName = (string) ($product->category->name ?? '');
    $discountPercent = $isOnSale && $basePrice > 0 ? (int) round((($basePrice - $displayPrice) / $basePrice) * 100) : 0;

    $productHref = $href ?: route('user.product-detail', ['product' => $product->slug ?? $product->id]);
@endphp

<div data-product-card="{{ $removeOnUnwishlist ? '1' : '0' }}" data-product-id="{{ $productId }}"
    class="group rounded-3xl bg-white overflow-hidden border border-[#efe7dc] shadow-[0_6px_20px_rgba(20,20,20,0.05)] hover:shadow-[0_14px_38px_rgba(20,20,20,0.12)] hover:-translate-y-1 transition-all duration-300">
    <div class="relative aspect-3/4 overflow-hidden bg-[#f6f2ed]">
        <a href="{{ $productHref }}" class="block h-full">
            <img src="{{ $imageUrl }}" alt="{{ $name }}"
                onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                class="w-full h-full object-cover group-hover:scale-105 group-hover:brightness-105 transition duration-500">

            <div
                class="absolute inset-x-0 bottom-0 h-24 bg-linear-to-t from-black/25 to-transparent pointer-events-none">
            </div>

            @if ($isOnSale)
                <div class="absolute top-3 left-3 z-10">
                    <span
                        class="bg-[#ff4d4f] text-white text-[10px] px-2.5 py-1 rounded-full uppercase font-extrabold tracking-wide shadow">
                        -{{ $discountPercent }}%
                    </span>
                </div>
            @endif
        </a>

        <div
            class="absolute z-20 left-3 right-3 bottom-3 p-2 rounded-2xl bg-white/88 backdrop-blur-md border border-white/80 shadow-lg">
            <livewire:user.product-quick-actions :product-id="$productId" :wishlisted="$isWhistlisted" :key="'product-quick-actions-' . $productId" />
        </div>
    </div>

    <div class="p-4 space-y-2.5">
        @if ($categoryName !== '')
            <span
                class="inline-flex items-center rounded-full bg-[#f6efe4] text-[#9d7a4a] px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide">
                {{ $categoryName }}
            </span>
        @endif

        <a href="{{ $productHref }}"
            class="text-sm font-semibold text-gray-800 line-clamp-2 min-h-11 hover:text-[#bc9c75] transition-colors leading-5">
            {{ $name }}
        </a>

        <div class="pt-1 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 flex-wrap">
                <span
                    class="text-[#bc9c75] font-black text-[17px] leading-none">{{ number_format($displayPrice, 0, ',', '.') }}đ</span>
                @if ($isOnSale)
                    <span
                        class="text-xs text-gray-400 line-through">{{ number_format($basePrice, 0, ',', '.') }}đ</span>
                @endif
            </div>

            @if ($soldLabel)
                <span class="text-xs text-gray-500">{{ $soldLabel }}</span>
            @endif
        </div>
    </div>
</div>
