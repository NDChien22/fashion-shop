@php
    $displayBanners = collect($banners ?? [])->values();

    $resolveBannerImage = function ($path) {
        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/'])
            ? $path
            : asset('storage/' . ltrim($path, '/'));
    };
@endphp

@if ($displayBanners->isNotEmpty())
    <div class="relative overflow-hidden w-full">
        <div class="relative overflow-hidden bg-gray-100 group banner-group rounded-2xl md:rounded-3xl shadow-sm">
            <div class="animate-banner" id="banner-list" style="width: {{ max($displayBanners->count(), 1) * 100 }}%;">
                @foreach ($displayBanners as $banner)
                    <div class="banner-item" style="width: {{ 100 / max($displayBanners->count(), 1) }}%;">
                        @php
                            $bannerLink = method_exists($banner, 'targetUrl')
                                ? $banner->targetUrl()
                                : (($banner->banner_type ?? 'category') === 'collection'
                                    ? route('user.collection')
                                    : route('user.product'));
                        @endphp
                        <a href="{{ $bannerLink }}" class="block">
                            <img src="{{ $resolveBannerImage($banner->image_url) }}" alt="{{ $banner->title }}"
                                class="w-full h-52 sm:h-64 md:h-72 lg:h-80 object-cover">
                        </a>
                    </div>
                @endforeach
            </div>

            @if ($displayBanners->count() > 1)
                <button id="prev-btn"
                    class="absolute z-50 left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/20 hover:bg-white backdrop-blur-md rounded-full flex items-center justify-center text-white hover:text-black opacity-0 group-hover:opacity-100 transition-all duration-300 nav-btn">
                    <i class="ri-arrow-left-s-line text-2xl"></i>
                </button>
                <button id="next-btn"
                    class="absolute z-50 right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/20 hover:bg-white backdrop-blur-md rounded-full flex items-center justify-center text-white hover:text-black opacity-0 group-hover:opacity-100 transition-all duration-300 nav-btn">
                    <i class="ri-arrow-right-s-line text-2xl"></i>
                </button>
                <div class="absolute bottom-4 md:bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2">
                    @foreach ($displayBanners as $index => $unusedBanner)
                        <div class="h-1.5 rounded-full transition-all cursor-pointer" data-banner-dot
                            onclick="window.bannerCarousel?.goToSlide({{ $index }})"></div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
