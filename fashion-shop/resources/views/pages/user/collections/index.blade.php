@extends('layouts.user-layout')

@section('title', 'Bộ Sưu Tập')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-[1400px] mx-auto px-4 py-8">
                <nav class="text-sm text-gray-600 mb-4">
                    <a href="{{ route('dashboard') }}" class="hover:text-[#bc9c75] transition">Trang chủ</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900 font-semibold">Bộ Sưu Tập</span>
                </nav>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Bộ Sưu Tập</h1>
                <p class="text-gray-600 max-w-2xl">Khám phá những bộ sưu tập thời trang được lựa chọn kỹ lưỡng cho bạn. Tìm
                    kiếm bộ sưu tập phù hợp với phong cách của bạn.</p>
            </div>
        </div>

        <!-- Collections Grid -->
        <div class="max-w-[1400px] mx-auto px-4 py-12">
            @if ($collections->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach ($collections as $collection)
                        <a href="{{ route('user.collection', $collection->slug) }}"
                            class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">

                            <!-- Thumbnail -->
                            <div class="relative h-80 bg-gray-200 overflow-hidden">
                                @if ($collection->thumbnail_url)
                                    <img src="{{ asset('storage/' . $collection->thumbnail_url) }}"
                                        alt="{{ $collection->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-[#bc9c75] to-[#9d7a4a] flex items-center justify-center">
                                        <i class="ri-image-add-line text-white text-6xl opacity-30"></i>
                                    </div>
                                @endif

                                <!-- Overlay -->
                                <div
                                    class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-all duration-300">
                                </div>

                                <!-- View Button -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <button
                                        class="bg-white text-[#bc9c75] font-bold py-3 px-8 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-75 group-hover:scale-100 hover:bg-[#bc9c75] hover:text-white">
                                        Xem Bộ Sưu Tập
                                    </button>
                                </div>

                                <!-- Product Count Badge -->
                                <div
                                    class="absolute top-4 right-4 bg-[#ff4d4f] text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                                    {{ $collection->products_count }} sản phẩm
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="bg-white p-6">
                                <h3
                                    class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#bc9c75] transition-colors">
                                    {{ $collection->name }}
                                </h3>

                                @if ($collection->description)
                                    <p class="text-gray-600 text-sm line-clamp-2">
                                        {{ $collection->description }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Statistics -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-[#bc9c75] mb-2">{{ $collections->count() }}</div>
                            <div class="text-gray-600 text-sm">Bộ Sưu Tập</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-[#bc9c75] mb-2">{{ $collections->sum('products_count') }}
                            </div>
                            <div class="text-gray-600 text-sm">Sản Phẩm</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-[#bc9c75] mb-2">
                                {{ number_format($collections->avg('products_count')) }}</div>
                            <div class="text-gray-600 text-sm">TB/Bộ Sưu Tập</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-[#bc9c75] mb-2">100%</div>
                            <div class="text-gray-600 text-sm">Chất Lượng</div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
                    <i class="ri-inbox-line text-6xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Chưa có bộ sưu tập nào</h3>
                    <p class="text-gray-500 mb-6">Các bộ sưu tập sẽ sớm có mặt tại cửa hàng của chúng tôi</p>
                    <a href="{{ route('user.product') }}"
                        class="inline-block bg-[#bc9c75] text-white font-bold py-3 px-8 rounded-full hover:bg-[#a68560] transition">
                        <i class="ri-shopping-bag-line mr-2"></i>
                        Xem Sản Phẩm
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
