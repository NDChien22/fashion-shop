@extends('layouts.admin-layout')
@section('title', 'Thêm sản phẩm')

@section('page-header')

    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Quản lý sản phẩm
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Trang chính
        </span> /
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Quản lý sản phẩm
        </span>/
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Thêm sản phẩm</span>
    </p>
@endsection

@section('content')
    <div>
        @php
            $categoryErrors = $errors->getBag('category');
        @endphp

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="add-product-form" action="{{ route('admin.add-product-handler') }}" method="POST"
            enctype="multipart/form-data" data-old-variants='@json(old('variants', []))'>
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">Thông tin cơ bản</h3>

                        <div>
                            <label for="product_code" class="text-[11px] font-bold text-gray-400 uppercase">Mã sản
                                phẩm</label>
                            <input type="text" id="product_code" name="product_code" value="{{ old('product_code') }}"
                                placeholder="Ví dụ: PRD-TSHIRT-001"
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                        </div>

                        <div>
                            <label for="name" class="text-[11px] font-bold text-gray-400 uppercase">Tên sản phẩm</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Ví dụ: Áo sơ mi lụa cao cấp"
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                        </div>

                        <div>
                            <label for="description" class="text-[11px] font-bold text-gray-400 uppercase">Mô tả sản
                                phẩm</label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="base_price" class="text-[11px] font-bold text-gray-400 uppercase">Giá
                                    bán</label>
                                <input type="number" id="base_price" name="base_price" min="0" step="0.01"
                                    value="{{ old('base_price') }}"
                                    class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                            </div>

                            <div>
                                <label for="is_active" class="text-[11px] font-bold text-gray-400 uppercase">Trạng
                                    thái</label>
                                <select id="is_active" name="is_active"
                                    class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Đang bán
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Ngừng bán
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="category_id" class="text-[11px] font-bold text-gray-400 uppercase">Danh
                                    mục</label>
                                <select id="category_id" name="category_id"
                                    class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                    <option value="">-- Chọn danh mục --</option>
                                    @forelse ($parentCategories as $parentCategory)
                                        @if ($parentCategory->children->isNotEmpty())
                                            <optgroup label="{{ $parentCategory->name }}">
                                                @foreach ($parentCategory->children as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @else
                                            <option value="{{ $parentCategory->id }}"
                                                {{ old('category_id') == $parentCategory->id ? 'selected' : '' }}>
                                                {{ $parentCategory->name }}
                                            </option>
                                        @endif
                                    @empty
                                        <option value="" disabled>Chưa có danh mục</option>
                                    @endforelse
                                    <option value="__add_category__">+ Thêm danh mục</option>
                                </select>
                            </div>

                            <div>
                                <label for="collection_id" class="text-[11px] font-bold text-gray-400 uppercase">Bộ sưu
                                    tập</label>
                                <select id="collection_id" name="collection_id"
                                    class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                    <option value="">-- Không chọn --</option>
                                    @foreach ($collections as $collection)
                                        <option value="{{ $collection->id }}"
                                            {{ old('collection_id') == $collection->id ? 'selected' : '' }}>
                                            {{ $collection->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">Biến thể sản phẩm</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <input id="size" type="text" placeholder="Size"
                                class="bg-gray-50 rounded-xl py-2 px-3 text-xs border border-gray-100 focus:ring-1 focus:ring-[#bc9c75] outline-none">
                            <input id="color" type="text" placeholder="Màu"
                                class="bg-gray-50 rounded-xl py-2 px-3 text-xs border border-gray-100 focus:ring-1 focus:ring-[#bc9c75] outline-none">
                            <input id="qty" type="number" placeholder="Số lượng"
                                class="bg-gray-50 rounded-xl py-2 px-3 text-xs border border-gray-100 focus:ring-1 focus:ring-[#bc9c75] outline-none">
                            <button type="button" onclick="addVariant()"
                                class="bg-[#bc9c75] text-white rounded-xl text-xs py-2 hover:bg-[#a68a68]">Thêm</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs mt-3">
                                <thead class="border-b text-gray-500 font-medium">
                                    <tr>
                                        <th class="text-left py-2">Size</th>
                                        <th class="text-left">Màu</th>
                                        <th class="text-left">Số lượng</th>
                                        <th class="text-right">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="variantList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">Hình ảnh sản phẩm</h3>

                        <div>
                            <label for="main_image" class="text-[11px] font-bold text-gray-400 uppercase">Ảnh
                                chính</label>
                            <input type="file" id="main_image" name="main_image" accept="image/*"
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                            <p class="mt-1 text-[11px] text-gray-400">Định dạng: jpg, jpeg, png, webp. Tối đa 2MB.</p>
                            <div id="main-image-preview-wrapper"
                                class="mt-3 rounded-xl overflow-hidden border border-gray-100 bg-gray-50 hidden">
                                <img id="main-image-preview" src="/asset/img/product-1.jpg" alt="Main image preview"
                                    class="w-full h-48 object-cover">
                            </div>
                        </div>

                        <div>
                            <label for="gallery_images" class="text-[11px] font-bold text-gray-400 uppercase">Album
                                ảnh</label>
                            <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                            <p class="mt-1 text-[11px] text-gray-400">Chọn nhiều ảnh cùng lúc, tối đa 10 ảnh.</p>
                            <div id="gallery-preview" class="mt-3 grid grid-cols-3 gap-2"></div>
                        </div>
                    </div>

                    <div class="mb-6 flex justify-end gap-3">
                        <a href="{{ route('admin.product-manager') }}"
                            class="px-6 py-2 border border-gray-200 rounded-xl text-xs font-semibold text-gray-500 hover:bg-gray-50">
                            Quay lại
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-[#bc9c75] text-white rounded-xl text-xs font-semibold shadow-sm hover:opacity-90">
                            Thêm sản phẩm
                        </button>
                    </div>
                </div>
            </div>
            <div id="variant-hidden-inputs"></div>
        </form>

        <div id="categorySetupModal" data-auto-open="{{ $showCategorySetup ? '1' : '0' }}"
            data-keep-open="{{ session('category_modal_open') ? '1' : '0' }}"
            class="fixed inset-0 z-100 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white w-full max-w-3xl rounded-4xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-[18px] font-bold text-gray-800">Thiết lập danh mục</h3>
                    </div>
                    <button type="button" onclick="closeCategorySetupModal()"
                        class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-gray-400"></i>
                    </button>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="lg:col-span-2 space-y-3">
                        @if (session('category_success'))
                            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                                {{ session('category_success') }}
                            </div>
                        @endif

                        @if ($categoryErrors->any())
                            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($categoryErrors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('admin.add-product-category-handler') }}" method="POST"
                        class="bg-white rounded-xl border border-gray-200 p-4 space-y-3" data-category-form="parent">
                        @csrf
                        <input type="hidden" name="type" value="parent">

                        <p class="text-xs font-bold text-gray-600 uppercase">Thêm parent_cate</p>
                        <input type="text" name="name" placeholder="Ví dụ: Thời trang nam"
                            class="w-full bg-gray-50 rounded-xl py-2.5 px-4 text-xs border border-gray-100">
                        <p class="hidden text-[11px] text-red-500" data-error-for="name"></p>

                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-[#bc9c75] text-white text-xs font-semibold hover:opacity-90">
                            Thêm parent_cate
                        </button>
                    </form>

                    <form action="{{ route('admin.add-product-category-handler') }}" method="POST"
                        class="bg-white rounded-xl border border-gray-200 p-4 space-y-3" data-category-form="child">
                        @csrf
                        <input type="hidden" name="type" value="child">

                        <p class="text-xs font-bold text-gray-600 uppercase">Thêm cate</p>
                        <select name="parent_id"
                            class="w-full bg-gray-50 rounded-xl py-2.5 px-4 text-xs border border-gray-100">
                            <option value="">-- Chọn parent_cate --</option>
                            @foreach ($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                            @endforeach
                        </select>
                        <p class="hidden text-[11px] text-red-500" data-error-for="parent_id"></p>

                        <input type="text" name="name" placeholder="Ví dụ: Áo polo"
                            class="w-full bg-gray-50 rounded-xl py-2.5 px-4 text-xs border border-gray-100">
                        <p class="hidden text-[11px] text-red-500" data-error-for="name"></p>

                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-[#bc9c75] text-white text-xs font-semibold hover:opacity-90">
                            Thêm cate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="/extra-assets/js/productmanager.js"></script>
@endpush
