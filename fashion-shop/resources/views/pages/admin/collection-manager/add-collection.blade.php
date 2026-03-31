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

    <div>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <form action="{{ route('admin.store-collection') }}" method="POST" enctype="multipart/form-data"
                    class="p-8 space-y-8">
                    @csrf

                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-100 rounded-[20px] p-10 bg-gray-50/30 hover:bg-[#fffbf7] hover:border-[#bc9c75]/30 transition-all cursor-pointer group"
                        onclick="document.getElementById('thumbnail').click()">
                        <div
                            class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm text-[#bc9c75] mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        </div>
                        <p class="font-bold text-gray-700">Tải lên ảnh bìa BST</p>
                        <p class="text-xs text-gray-400 mt-1">Khuyên dùng kích thước 1200x600px (Max 5MB)</p>
                        <input type="file" name="thumbnail" id="thumbnail" class="hidden" accept="image/*">
                        <p class="text-xs text-gray-500 mt-3" id="file-name"></p>
                    </div>

                    @error('thumbnail')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Tên bộ sưu tập</label>
                            <input type="text" name="name" placeholder="Ví dụ: Thu Đông 2026 - Urban Style"
                                class="w-full rounded-xl p-4 border border-gray-200 focus:border-[#bc9c75] focus:ring-4 focus:ring-[#bc9c75]/5 outline-none transition-all @error('name') border-red-500 @enderror"
                                value="{{ old('name') }}">
                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Mô tả (Không bắt buộc)</label>
                            <textarea name="description" rows="3" placeholder="Đôi dòng giới thiệu về cảm hứng của BST này..."
                                class="w-full rounded-xl p-4 border border-gray-200 focus:border-[#bc9c75] outline-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded"
                                    @checked(old('is_active', true))>
                                <span class="text-xs font-bold text-gray-500 uppercase">Kích hoạt BST này</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6 border-t flex justify-end gap-4">
                        <a href="{{ route('admin.product-collections') }}"
                            class="px-8 py-3.5 rounded-xl font-bold text-gray-400 hover:text-gray-600 transition-all">
                            Quay lại
                        </a>
                        <button type="submit"
                            class="bg-[#bc9c75] text-white px-10 py-3.5 rounded-xl font-bold shadow-lg shadow-[#bc9c75]/20 hover:brightness-95 active:scale-95 transition-all">
                            Thêm bộ sưu tập mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || '';
            document.getElementById('file-name').textContent = fileName ? 'Đã chọn: ' + fileName : '';
        });
    </script>

@endsection
