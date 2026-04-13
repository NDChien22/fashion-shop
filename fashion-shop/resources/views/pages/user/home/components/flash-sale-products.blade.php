@php
    $activeSales = collect($activeFlashSales ?? []);
    $flashItems = collect($flashSaleProducts ?? []);
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

@if ($activeSales->isNotEmpty() && $flashItems->isNotEmpty())
    <div class="bg-white py-12">
        <div class="container mx-auto px-4">
            <div
                class="flex flex-col md:flex-row items-center justify-between border-b-2 border-gray-100 pb-6 mb-8 gap-4">
                <div class="flex items-center gap-6">
                    <h2 class="text-3xl font-black text-gold italic uppercase tracking-tighter">Flash Sale</h2>
                </div>
                <a href="{{ route('user.product') }}"
                    class="text-sm font-bold text-gray-400 hover:text-gold transition underline uppercase tracking-widest">
                    Xem tất cả
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-6">
                @foreach ($flashItems as $product)
                    <a href="{{ route('user.product') }}" class="block group">
                        <div class="rounded-2xl bg-gray-50 overflow-hidden border border-gray-100">
                            <div class="aspect-3/4 overflow-hidden">
                                <img src="{{ $resolveProductImage($product->main_image_url) }}"
                                    alt="{{ $product->name }}" {{-- onerror="this.onerror=null;this.src='{{ $fallbackImage }}';" --}}
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <div class="p-3">
                                <h4 class="text-sm font-semibold text-gray-800 line-clamp-2 min-h-10">
                                    {{ $product->name }}</h4>
                                <div class="mt-2 flex items-end gap-2">
                                    <span class="text-[#bc9c75] font-black text-base">
                                        {{ number_format((float) ($product->sale_price ?? $product->base_price), 0, ',', '.') }}đ
                                    </span>
                                    <span class="text-xs text-gray-400 line-through">
                                        {{ number_format((float) $product->base_price, 0, ',', '.') }}đ
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
