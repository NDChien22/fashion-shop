@extends('layouts.admin-layout')
@section('title', 'Bộ sưu tập')

@section('page-header')
    <h1 class="text-xl font-semibold text-gray-800">Bộ sưu tập</h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Trang chính</span> /
        <span class="text-[#bc9c75] font-medium">Bộ sưu tập</span>
    </p>
@endsection

@section('content')

    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($collections as $collection)
                <div
                    class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-[#bc9c75] transition-all cursor-pointer group">
                    <a href="{{ route('admin.show-collection', $collection->slug) }}">
                        <div
                            class="w-full h-40 bg-[#fffbf7] rounded-2xl mb-4 flex items-center justify-center border border-orange-50 overflow-hidden">
                            @if ($collection->thumbnail_url)
                                <img src="{{ asset('storage/' . $collection->thumbnail_url) }}" alt="{{ $collection->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            @else
                                <i
                                    class="fa-solid fa-images text-4xl text-[#bc9c75]/30 group-hover:scale-110 transition-transform"></i>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg">{{ $collection->name }}</h3>
                        <p class="text-sm text-gray-400 mt-1">{{ $collection->products_count ?? 0 }} sản phẩm • Cập nhật
                            {{ $collection->updated_at->diffForHumans() }}</p>
                    </a>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.edit-collection', $collection->slug) }}"
                            class="flex-1 bg-blue-50 text-blue-600 px-3 py-2 rounded-lg font-bold text-sm flex items-center justify-center hover:bg-blue-100 transition-all">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('admin.destroy-collection', $collection->slug) }}" method="POST"
                            class="flex-1" data-confirm-delete="1"
                            data-confirm-message="Bạn chắc chắn muốn xóa bộ sưu tập này?">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-full bg-red-50 text-red-600 px-3 py-2 rounded-lg font-bold text-sm flex items-center justify-center hover:bg-red-100 transition-all">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fa-solid fa-inbox text-6xl text-gray-200 mb-4"></i>
                    <p class="text-gray-400 font-bold">Chưa có bộ sưu tập nào</p>
                </div>
            @endforelse

            <div onclick="window.location.href='{{ route('admin.create-collection') }}'"
                class="border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center p-6 hover:bg-white hover:border-[#bc9c75] transition-all cursor-pointer text-gray-400 hover:text-[#bc9c75]">
                <i class="fa-solid fa-plus-circle text-3xl mb-2"></i>
                <span class="font-bold">Tạo BST mới</span>
            </div>
        </div>
    </div>

@endsection
