@extends('layouts.admin-layout')
@section('title', 'Thông tin bộ sưu tập')

@section('page-header')
    <h1 class="text-xl font-semibold text-gray-800">Bộ sưu tập</h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Trang chính</span> /
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Bộ sưu tập</span> /
        <span class="text-[#bc9c75] font-medium">{{ $collection->name }}</span>
    </p>
@endsection

@section('content')

    <div>
        <div class="space-y-6">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.product-collections') }}"
                        class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div
                        class="flex items-center gap-3 bg-[#fffbf7] px-4 py-2 rounded-xl border border-orange-50 text-[#bc9c75]">
                        <i class="fa-solid fa-images"></i>
                        <span class="font-bold">{{ $collection->name }}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.edit-collection', $collection->slug) }}"
                        class="bg-blue-50 text-blue-600 px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:bg-blue-100 transition-all">
                        <i class="fa-regular fa-pen-to-square"></i> Chỉnh sửa
                    </a>
                    <button type="button"
                        onclick="document.getElementById('productSelectionModal').classList.remove('hidden')"
                        class="bg-[#bc9c75] text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Thêm sản phẩm
                    </button>
                </div>
            </div>

            @if ($collection->thumbnail_url)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <img src="{{ asset('storage/' . $collection->thumbnail_url) }}" alt="{{ $collection->name }}"
                        class="w-full h-64 object-cover">
                </div>
            @endif

            @if ($collection->description)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-3">Mô tả</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $collection->description }}</p>
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Sản phẩm trong bộ sưu tập
                        <span class="text-gray-600">({{ $products->total() }})</span>
                    </h3>
                </div>

                @if ($products->count() > 0)
                    <div class="divide-y divide-gray-50">
                        @forelse ($products as $product)
                            <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                                <div class="flex items-center gap-6">
                                    <div
                                        class="w-20 h-24 bg-gray-100 rounded-2xl flex items-center justify-center border border-gray-50 overflow-hidden relative">
                                        @if ($product->main_image_url)
                                            <img src="{{ asset($product->main_image_url) }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover cursor-pointer hover:opacity-80"
                                                onerror="this.parentElement.innerHTML='<i class=\"fa-solid fa-image text-gray-300 text-2xl\"></i>
                                        @else
                                            <div class="text-center">
                                                <i class="fa-solid fa-image text-gray-300 text-2xl"></i>
                                                <p class="text-[9px] text-gray-400 mt-1">Không có ảnh</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-lg">{{ $product->name }}</h4>
                                        <p class="text-sm text-gray-400 mt-1">Mã: <span
                                                class="text-[#bc9c75]">{{ $product->product_code }}</span></p>
                                        <div class="flex gap-2 mt-2">
                                            @if ($product->is_active)
                                                <span
                                                    class="text-[9px] bg-green-50 text-green-600 px-2 py-0.5 rounded font-bold uppercase">Đang
                                                    bán</span>
                                            @else
                                                <span
                                                    class="text-[9px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-bold uppercase">Ẩn</span>
                                            @endif
                                            <span
                                                class="text-[9px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-bold uppercase">{{ $product->skus_count ?? 0 }}
                                                SKU</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.edit-product', $product->slug) }}"
                                        class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500 transition-all">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.remove-product-from-collection', $collection->slug) }}"
                                        method="POST" style="display: inline;"
                                        onsubmit="return confirm('Bạn chắc chắn muốn xóa sản phẩm này khỏi bộ sưu tập?')">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button
                                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 transition-all border-0 bg-transparent cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>

                    @if ($products->hasPages())
                        <div class="p-6 border-t border-gray-50 flex justify-center">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <i class="fa-solid fa-inbox text-6xl text-gray-200 mb-4"></i>
                        <p class="text-gray-400 font-bold">Chưa có sản phẩm nào trong bộ sưu tập này</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Selection Modal -->
    <div id="productSelectionModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-3xl w-full max-w-2xl mx-4 max-h-96 overflow-y-auto">
            <div class="sticky top-0 bg-white p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg">Chọn sản phẩm để thêm vào BST</h3>
                <button type="button" onclick="document.getElementById('productSelectionModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 border-0 bg-transparent cursor-pointer">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            @if ($availableProducts->count() > 0)
                <form action="{{ route('admin.add-products-to-collection', $collection->slug) }}" method="POST"
                    class="p-6 space-y-4">
                    @csrf
                    @foreach ($availableProducts as $product)
                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="w-4 h-4">
                            <span class="flex-1 text-gray-700">{{ $product->name }}</span>
                        </label>
                    @endforeach

                    <div class="flex justify-end gap-4 border-t border-gray-100 pt-4 mt-6">
                        <button type="button"
                            onclick="document.getElementById('productSelectionModal').classList.add('hidden')"
                            class="px-6 py-2.5 rounded-xl font-bold text-gray-400 hover:text-gray-600 border-0 bg-transparent cursor-pointer">
                            Hủy
                        </button>
                        <button type="submit"
                            class="bg-[#bc9c75] text-white px-6 py-2.5 rounded-xl font-bold hover:brightness-95 cursor-pointer border-0">
                            Thêm
                        </button>
                    </div>
                </form>
            @else
                <div class="p-12 text-center">
                    <p class="text-gray-400 font-bold mb-6">Không có sản phẩm để thêm</p>
                    <button type="button"
                        onclick="document.getElementById('productSelectionModal').classList.add('hidden')"
                        class="px-6 py-2.5 rounded-xl font-bold text-gray-400 hover:text-gray-600 border-0 bg-transparent cursor-pointer">
                        Đóng
                    </button>
                </div>
            @endif
        </div>
    </div>

@endsection
