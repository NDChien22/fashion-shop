@extends('layouts.admin-layout')
@section('title', 'Tạo BST mới')
@section('content')

    <div>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <form id="form-add-collection" class="p-8 space-y-8">
                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-100 rounded-[20px] p-10 bg-gray-50/30 hover:bg-[#fffbf7] hover:border-[#bc9c75]/30 transition-all cursor-pointer group">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm text-[#bc9c75] mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        </div>
                        <p class="font-bold text-gray-700">Tải lên ảnh bìa BST</p>
                        <p class="text-xs text-gray-400 mt-1">Khuyên dùng kích thước 1200x600px (Max 5MB)</p>
                        <input type="file" class="hidden" id="collection-cover">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Tên bộ sưu tập</label>
                            <input type="text" placeholder="Ví dụ: Thu Đông 2026 - Urban Style" 
                                class="w-full rounded-xl p-4 border border-gray-200 focus:border-[#bc9c75] focus:ring-4 focus:ring-[#bc9c75]/5 outline-none transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Loại BST</label>
                            <select class="w-full rounded-xl p-4 border border-gray-200 outline-none focus:border-[#bc9c75] bg-white">
                                <option>Thời trang Nam</option>
                                <option>Thời trang Nữ</option>
                                <option>Phụ kiện</option>
                                <option>Limited Edition</option>
                            </select>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Mô tả (Không bắt buộc)</label>
                            <textarea rows="3" placeholder="Đôi dòng giới thiệu về cảm hứng của BST này..." 
                                    class="w-full rounded-xl p-4 border border-gray-200 focus:border-[#bc9c75] outline-none"></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t flex justify-end gap-4">
                        <button onclick="loadPage('collection')" class="px-8 py-3.5 rounded-xl font-bold text-gray-400 hover:text-gray-600 transition-all">
                            Quay lại
                        </button>
                        <button class="bg-[#bc9c75] text-white px-10 py-3.5 rounded-xl font-bold shadow-lg shadow-[#bc9c75]/20 hover:brightness-95 active:scale-95 transition-all">
                            Thêm bộ sưu tập mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
