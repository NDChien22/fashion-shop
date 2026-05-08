@php
    $collectionItems = collect($featuredCollections ?? []);
    $fallbackImage = 'https://placehold.co/800x1000/f3f4f6/9ca3af?text=Collection';

    $resolveCollectionImage = function ($path) {
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

@if ($collectionItems->isNotEmpty())
    <section class="py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-wider">Bộ sưu tập nổi bật</h2>
                    <div class="h-1 w-20 bg-[#bc9c75] mt-2"></div>
                </div>
                <a wire:navigate href="{{ route('user.product') }}"
                    class="text-[#bc9c75] font-medium hover:underline transition-all">
                    Xem tất cả <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($collectionItems as $collection)
                    <div class="relative rounded-2xl overflow-hidden group cursor-pointer shadow-lg">
                        <img src="{{ $resolveCollectionImage($collection->thumbnail_url) }}"
                            alt="{{ $collection->name }}" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                            class="w-full h-full object-cover aspect-3/4 group-hover:scale-110 transition duration-700">
                        <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-[#e07b39]/90 to-transparent p-6">
                            <h3 class="text-white font-black text-xl uppercase tracking-tighter line-clamp-2">
                                {{ $collection->name }}
                            </h3>
                            <p class="text-white/90 text-xs mt-1">{{ number_format($collection->products_count) }} sản
                                phẩm
                            </p>
                            <a wire:navigate href="{{ route('user.collection', $collection) }}"
                                class="text-white text-xs underline mt-2 inline-block hover:no-underline uppercase font-bold">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
