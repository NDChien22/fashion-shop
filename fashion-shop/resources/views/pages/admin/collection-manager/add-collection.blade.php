@extends('layouts.admin-layout')
@section('title', 'Tạo BST mới')

@section('page-header')
    <h1 class="text-xl font-semibold text-gray-800">Bộ sưu tập</h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Trang chính</span> /
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Bộ sưu tập</span> /
        <span class="text-[#bc9c75] font-medium">Tạo BST mới</span>
    </p>
@endsection

@section('content')

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <form action="{{ route('admin.store-collection') }}" method="POST" enctype="multipart/form-data"
                class="p-8 lg:p-10 space-y-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    <div class="lg:col-span-2 space-y-3">
                        <p class="text-xs uppercase tracking-wider font-semibold text-gray-400">Ảnh bìa</p>
                        <div onclick="document.getElementById('thumbnail').click()"
                            class="relative min-h-64 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/40 p-6 flex items-center justify-center cursor-pointer hover:border-[#bc9c75]/40 hover:bg-[#fffbf7] transition-all">
                            <img id="thumbnail-preview" src="" alt="Preview"
                                class="hidden absolute inset-0 w-full h-full object-cover rounded-2xl">
                            <div id="thumbnail-placeholder" class="text-center text-gray-500">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-[#bc9c75]"></i>
                                <p class="font-semibold mt-3">Tải ảnh đại diện bộ sưu tập</p>
                                <p class="text-xs text-gray-400 mt-1">Khuyên dùng 1200x600px, tối đa 5MB</p>
                            </div>
                        </div>
                        <input type="file" name="thumbnail" id="thumbnail" class="hidden" accept="image/*">
                        <p class="text-xs text-gray-500" id="file-name"></p>
                        @error('thumbnail')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="lg:col-span-3 space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tên bộ sưu tập</label>
                            <input type="text" name="name" placeholder="Ví dụ: Thu Đông 2026 - Urban Style"
                                class="w-full rounded-xl p-4 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200' }} focus:border-[#bc9c75] focus:ring-4 focus:ring-[#bc9c75]/5 outline-none transition-all"
                                value="{{ old('name') }}">
                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Mô tả</label>
                            <textarea name="description" rows="5" placeholder="Đôi dòng giới thiệu về cảm hứng và định hướng của bộ sưu tập..."
                                class="w-full rounded-xl p-4 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-200' }} focus:border-[#bc9c75] focus:ring-4 focus:ring-[#bc9c75]/5 outline-none">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded mt-0.5"
                                    @checked(old('is_active', true))>
                                <span>
                                    <span class="text-sm font-semibold text-gray-700">Kích hoạt bộ sưu tập ngay</span>
                                    <span class="text-xs text-gray-400 block mt-0.5">Bật để bộ sưu tập sẵn sàng cho việc thêm sản phẩm và quản lý.</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('admin.product-collections') }}"
                        class="px-7 py-3 rounded-xl font-semibold text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all">
                        Quay lại
                    </a>
                    <button type="submit"
                        class="bg-[#bc9c75] text-white px-8 py-3 rounded-xl font-semibold shadow-lg shadow-[#bc9c75]/20 hover:brightness-95 transition-all">
                        Lưu bộ sưu tập
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileNameElement = document.getElementById('file-name');
            const preview = document.getElementById('thumbnail-preview');
            const placeholder = document.getElementById('thumbnail-placeholder');

            if (!file) {
                fileNameElement.textContent = '';
                preview.classList.add('hidden');
                preview.src = '';
                placeholder.classList.remove('hidden');
                return;
            }

                fileNameElement.textContent = 'Đã chọn: ' + file.name;
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        });
    </script>

@endsection
