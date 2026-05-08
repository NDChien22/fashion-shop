@extends('layouts.user-static-layout')
@section('title', 'Whistlist')

@section('main-content')
    <div class="container mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold uppercase mb-8 border-b-2 border-red-400 inline-block pb-2">Danh sách whistlist</h2>

        @if (($products ?? collect())->count() > 0)
            <div id="whistlist-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <x-user.product-card :product="$product" :remove-on-unwishlist="true" />
                @endforeach
            </div>

            <div id="whistlist-empty-state" class="text-center py-20 hidden">
                <i class="ri-heart-line text-6xl text-gray-200"></i>
                <p class="text-gray-500 mt-4">Whistlist của bạn đang trống.</p>
                <a href="{{ route('user.product') }}"
                    class="inline-block mt-6 border border-black px-8 py-2 hover:bg-black hover:text-white transition">Khám
                    phá ngay</a>
            </div>
        @else
            <div id="whistlist-empty-state" class="text-center py-20">
                <i class="ri-heart-line text-6xl text-gray-200"></i>
                <p class="text-gray-500 mt-4">Whistlist của bạn đang trống.</p>
                <a href="{{ route('user.product') }}"
                    class="inline-block mt-6 border border-black px-8 py-2 hover:bg-black hover:text-white transition">Khám
                    phá ngay</a>
            </div>
        @endif
    </div>
@endsection
