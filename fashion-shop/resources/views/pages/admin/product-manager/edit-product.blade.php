@extends('layouts.admin-layout')
@section('title', 'Chỉnh sửa sản phẩm')

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
        </span> /

        <span class="text-[#bc9c75] font-medium">Chỉnh sửa sản phẩm</span>
    </p>
@endsection

@section('content')
    <div>
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $fallbackVariants = $product->skus
                ->map(
                    fn($sku) => [
                        'size' => $sku->size,
                        'color' => $sku->color,
                        'stock' => $sku->stock,
                    ],
                )
                ->values()
                ->all();

            $variantsForHydration = old('variants', $fallbackVariants);
            $galleryImages = is_array($product->gallery_image_urls) ? $product->gallery_image_urls : [];
            $mainImageUrl = $product->main_image_url ? asset($product->main_image_url) : '/asset/img/product-1.jpg';
            $basePriceValue = old('base_price', number_format((float) $product->base_price, 0, '', ''));
        @endphp

        <form id="add-product-form" action="{{ route('admin.update-product-handler', $product->slug) }}" method="POST"
            enctype="multipart/form-data" data-old-variants='@json($variantsForHydration)'>
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-gray-800">Thông tin cơ bản</h3>

                        <div>
                            <label for="product_code" class="text-[11px] font-bold text-gray-400 uppercase">Mã sản
                                phẩm</label>
                            <input type="text" id="product_code" name="product_code"
                                value="{{ old('product_code', $product->product_code) }}"
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                        </div>

                        <div>
                            <label for="name" class="text-[11px] font-bold text-gray-400 uppercase">Tên sản phẩm</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                        </div>

                        <div>
                            <label for="description" class="text-[11px] font-bold text-gray-400 uppercase">Mô tả sản
                                phẩm</label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="base_price" class="text-[11px] font-bold text-gray-400 uppercase">Giá
                                    bán</label>
                                <input type="number" id="base_price" name="base_price" min="0" step="0.01"
                                    value="{{ $basePriceValue }}"
                                    class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                            </div>

                            <div>
                                <label for="is_active" class="text-[11px] font-bold text-gray-400 uppercase">Trạng
                                    thái</label>
                                <select id="is_active" name="is_active"
                                    class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                    <option value="1"
                                        {{ old('is_active', (string) $product->is_active) === '1' ? 'selected' : '' }}>
                                        Đang bán
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', (string) $product->is_active) === '0' ? 'selected' : '' }}>
                                        Ngừng bán
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="category_id" class="text-[11px] font-bold text-gray-400 uppercase">Danh
                                    mục</label>
                                <select id="category_id" name="category_id"
                                    class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach ($parentCategories as $parentCategory)
                                        @if ($parentCategory->children->isNotEmpty())
                                            <optgroup label="{{ $parentCategory->name }}">
                                                @foreach ($parentCategory->children as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ (string) old('category_id', (string) $product->category_id) === (string) $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @else
                                            <option value="{{ $parentCategory->id }}"
                                                {{ (string) old('category_id', (string) $product->category_id) === (string) $parentCategory->id ? 'selected' : '' }}>
                                                {{ $parentCategory->name }}
                                            </option>
                                        @endif
                                    @endforeach
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
                                            {{ (string) old('collection_id', (string) $product->collection_id) === (string) $collection->id ? 'selected' : '' }}>
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
                            <p class="mt-1 text-[11px] text-gray-400">Để trống nếu không đổi ảnh chính.</p>
                            <div id="main-image-preview-wrapper" data-has-image="1"
                                class="mt-3 rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                                <img id="main-image-preview" src="{{ $mainImageUrl }}" alt="Main image preview"
                                    class="w-full h-48 object-cover">
                            </div>
                        </div>

                        <div>
                            <label for="gallery_images" class="text-[11px] font-bold text-gray-400 uppercase">Album
                                ảnh</label>
                            <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple
                                class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                            <p class="mt-1 text-[11px] text-gray-400">Ảnh cũ được giữ nguyên. Ảnh mới sẽ được thêm vào
                                album.</p>

                            <div id="gallery-preview" class="mt-3 space-y-2">
                                <div id="gallery-existing-preview" class="grid grid-cols-3 gap-2">
                                    @foreach ($galleryImages as $image)
                                        <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-100"
                                            data-existing-path="{{ $image }}">
                                            <img src="{{ asset($image) }}" alt="Gallery image"
                                                class="w-full h-full object-cover">
                                            <button type="button" data-remove-existing
                                                class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/65 text-white text-[11px] leading-none hover:bg-red-500">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <div id="gallery-new-preview" class="grid grid-cols-3 gap-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 flex justify-end gap-3">
                        <a href="{{ route('admin.product-manager') }}"
                            class="px-6 py-2 border border-gray-200 rounded-xl text-xs font-semibold text-gray-500 hover:bg-gray-50">
                            Quay lại
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-[#bc9c75] text-white rounded-xl text-xs font-semibold shadow-sm hover:opacity-90">
                            Lưu sản phẩm
                        </button>
                    </div>
                </div>
            </div>

            <div id="variant-hidden-inputs"></div>
            <div id="removed-gallery-inputs"></div>
        </form>

        @if ($errors->has('variants'))
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('variants') }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="/extra-assets/js/productmanager.js"></script>
@endpush
</div>
