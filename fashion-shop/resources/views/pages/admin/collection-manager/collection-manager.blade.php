@extends('layouts.admin-layout')
@section('title', 'Bộ sưu tập')
@section('content')

    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div onclick="loadPage('collection-detail')" 
                class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-[#bc9c75] transition-all cursor-pointer group">
                <div class="w-full h-40 bg-[#fffbf7] rounded-2xl mb-4 flex items-center justify-center border border-orange-50">
                    <i class="fa-solid fa-images text-4xl text-[#bc9c75]/30 group-hover:scale-110 transition-transform"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg">Xuân Hè 2026</h3>
                <p class="text-sm text-gray-400 mt-1">24 sản phẩm • Cập nhật 2 ngày trước</p>
            </div>

            <div onclick="viewCollectionDetail('Bộ sưu tập Thu Đông 2025')" 
                class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-[#bc9c75] transition-all cursor-pointer group">
                <div class="w-full h-40 bg-gray-50 rounded-2xl mb-4 flex items-center justify-center border border-gray-100">
                    <i class="fa-solid fa-snowflake text-4xl text-blue-200 group-hover:scale-110 transition-transform"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-lg">Thu Đông 2025</h3>
                <p class="text-sm text-gray-400 mt-1">18 sản phẩm • Cập nhật 1 tuần trước</p>
            </div>

            <div onclick="loadPage('add-collection')" class="border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center p-6 hover:bg-white hover:border-[#bc9c75] transition-all cursor-pointer text-gray-400 hover:text-[#bc9c75]">
                <i class="fa-solid fa-plus-circle text-3xl mb-2"></i>
                <span class="font-bold">Tạo BST mới</span>
            </div>
        </div>
    </div>

@endsection
