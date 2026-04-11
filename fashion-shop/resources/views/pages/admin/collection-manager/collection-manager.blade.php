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

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 lg:p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="rounded-xl border border-gray-100 bg-gray-50/70 px-4 py-3">
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold">Tổng BST</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $collections->count() }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/70 px-4 py-3">
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold">Đang hoạt động</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $collections->where('is_active', 1)->count() }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50/70 px-4 py-3">
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold">Tổng sản phẩm</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $collections->sum('products_count') }}</p>
                </div>
                <a href="{{ route('admin.create-collection') }}"
                    class="rounded-xl border border-[#bc9c75]/20 bg-[#fffbf7] px-4 py-3 flex items-center justify-center gap-2 text-[#bc9c75] font-semibold hover:bg-[#fff6ec] transition-all">
                    <i class="fa-solid fa-plus"></i>
                    Tạo bộ sưu tập
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($collections as $collection)
                <div
                    class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-[#bc9c75] hover:shadow-md transition-all cursor-pointer group">
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
                        <p class="text-sm text-gray-400 mt-1">{{ $collection->products_count ?? 0 }} sản phẩm • Cập nhật {{ $collection->updated_at->diffForHumans() }}</p>
                        <span
                            class="inline-flex mt-3 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $collection->is_active ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                            {{ $collection->is_active ? 'Đang hoạt động' : 'Đã tắt' }}
                        </span>
                    </a>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.edit-collection', $collection->slug) }}"
                            class="flex-1 bg-blue-50 text-blue-600 px-3 py-2 rounded-lg font-bold text-sm flex items-center justify-center gap-2 hover:bg-blue-100 transition-all">
                            <i class="fa-regular fa-pen-to-square"></i>
                            Sửa
                        </a>
                        <form action="{{ route('admin.destroy-collection', $collection->slug) }}" method="POST"
                            class="flex-1" data-confirm-delete="1"
                            data-confirm-message="Bạn chắc chắn muốn xóa bộ sưu tập này?">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-full bg-red-50 text-red-600 px-3 py-2 rounded-lg font-bold text-sm flex items-center justify-center gap-2 hover:bg-red-100 transition-all">
                                <i class="fa-solid fa-trash-can"></i>
                                Xóa
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-dashed border-gray-200">
                    <i class="fa-solid fa-inbox text-6xl text-gray-200 mb-4"></i>
                    <p class="text-gray-400 font-bold">Chưa có bộ sưu tập nào</p>
                </div>
            @endforelse
        </div>
    </div>

@endsection
