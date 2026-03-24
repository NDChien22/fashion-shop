@extends('layouts.admin-layout')
@section('title', 'Thông tin bộ sưu tập')
@section('content')

    <div>
        <div class="space-y-6">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <button onclick="loadPage('collection')"
                        class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <div
                        class="flex items-center gap-3 bg-[#fffbf7] px-4 py-2 rounded-xl border border-orange-50 text-[#bc9c75]">
                        <i class="fa-solid fa-images"></i>
                        <span class="font-bold collection-title">Tên BST đang xem</span>
                    </div>
                </div>
                <button onclick="openProductSelectionModal()"
                    class="bg-[#bc9c75] text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Thêm sản phẩm vào BST
                </button>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Sản phẩm trong bộ sưu tập</h3>
                </div>

                <div class="divide-y divide-gray-50">
                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-6">
                            <div
                                class="w-20 h-24 bg-gray-100 rounded-2xl flex items-center justify-center border border-gray-50 overflow-hidden relative">
                                <img src="..." onclick="openModal('productDetailModal')"
                                    class="w-full h-full rounded-lg cursor-pointer hover:opacity-80">
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">Classic Wool Coat</h4>
                                <p class="text-sm text-gray-400 mt-1">Mã: <span class="text-[#bc9c75]">FW2026-001</span></p>
                                <div class="flex gap-2 mt-2">
                                    <span
                                        class="text-[9px] bg-green-50 text-green-600 px-2 py-0.5 rounded font-bold uppercase">Còn
                                        hàng</span>
                                    <span
                                        class="text-[9px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-bold uppercase">Size:
                                        L, XL</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="openForm('product')" class="w-8 h-8 text-gray-400 hover:text-red-500"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="w-8 h-8 text-gray-400 hover:text-red-500"><i
                                    class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </div>
                </div>

                <div class="p-4 text-center border-t border-gray-50">
                    <button class="text-[#bc9c75] font-bold text-sm hover:underline">Xem thêm sản phẩm</button>
                </div>
            </div>
        </div>
    </div>

@endsection
