@extends('layouts.admin-layout')
@section('title', 'Chỉnh sửa sản phẩm')
@section('content')

    <div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border space-y-4">
                    <h3 class="text-sm font-bold text-gray-800">Thông tin cơ bản</h3>
                    <div>
                        <label class="text-[11px] font-bold text-gray-400 uppercase">Mã sản phẩm</label>
                        <input type="text" id="prod-id" class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-400 uppercase">Tên sản phẩm</label>
                        <input type="text" id="prod-name" placeholder="Ví dụ: Áo sơ mi lụa cao cấp" class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-400 uppercase">Mô tả sản phẩm</label>
                        <textarea id="prod-desc" rows="4" class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase">Giá bán</label>
                            <input type="number" id="prod-price" class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase">Phân loại</label>
                            <select id="prod-cate" class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                <option value="">-- Không chọn --</option>
                                <option>Áo khoác</option>
                                <option>Áo thun</option>
                                <option>Quần</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase">Bộ sưu tập</label>
                            <select id="prod-cate" class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                <option value="">-- Không chọn --</option>
                                <option>...</option>
                                <option>...</option>
                                <option>...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border space-y-4">
                    <h3 class="text-sm font-bold text-gray-800">Biến thể sản phẩm</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <input id="size" type="text" placeholder="Size" class="bg-gray-50 rounded-xl py-2 px-3 text-xs border focus:ring-1 focus:ring-[#bc9c75] outline-none">
                        <input id="color" type="text" placeholder="Màu" class="bg-gray-50 rounded-xl py-2 px-3 text-xs border focus:ring-1 focus:ring-[#bc9c75] outline-none">
                        <input id="qty" type="number" placeholder="Số lượng" class="bg-gray-50 rounded-xl py-2 px-3 text-xs border focus:ring-1 focus:ring-[#bc9c75] outline-none">
                        <button onclick="addVariant()" class="bg-[#bc9c75] text-white rounded-xl text-xs py-2 hover:bg-[#a68a68]">Thêm</button>
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
                <div class="bg-white p-6 rounded-2xl shadow-sm border">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">Hình ảnh sản phẩm</h3>
                    <div class="relative group rounded-2xl overflow-hidden border border-gray-100 mb-4">
                        <img id="prod-img-main" src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400" class="w-full h-48 object-cover">
                    </div>
                    <div class="border-2 border-dashed border-gray-100 rounded-2xl p-4 flex flex-col items-center justify-center gap-2 cursor-pointer hover:bg-gray-50">
                        <i class="fa-solid fa-plus text-[#bc9c75]"></i>
                        <p class="text-[10px] text-gray-400 font-medium">Thay đổi ảnh khác</p>
                    </div>
                    
                    <div class="mt-8">
                        <label class="text-[14px] font-semibold text-gray-700 ml-1 mb-3 block">Album hình ảnh</label>
                        <div class="flex gap-4">
                            <div onclick="openAlbumModal()" class="relative w-24 h-24 rounded-2xl cursor-pointer group">
                                <div class="absolute inset-0 bg-gray-200 rounded-2xl translate-x-2 -translate-y-2 z-0"></div>
                                <div class="absolute inset-0 bg-gray-100 rounded-2xl translate-x-1 -translate-y-1 z-10"></div>
                                <div class="absolute inset-0 rounded-2xl overflow-hidden border-2 border-white shadow-md z-20">
                                    <img src="/asset/img/product-1.jpg" class="w-full h-full object-cover" alt="Album">
                                    <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-white">
                                        <span class="text-[16px] font-bold">+12</span>
                                        <span class="text-[10px]">Xem tất cả</span>
                                    </div>
                                </div>
                            </div>

                            <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-[#bc9c75] hover:bg-[#fffbf7] transition-all group">
                                <i class="fa-solid fa-plus text-[#bc9c75] text-sm"></i>
                                <span class="text-[11px] font-semibold text-gray-400 group-hover:text-[#bc9c75]">Thêm mới</span>
                            </div>
                        </div>
                    </div>   
                    
                    
                    <div id="albumModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                        <div class="bg-white w-full max-w-2xl rounded-[32px] shadow-2xl overflow-hidden">
                            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="text-[16px] font-bold text-gray-800">Tất cả hình ảnh (12)</h3>
                                <button onclick="closeAlbumModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center">
                                    <i class="fa-solid fa-xmark text-gray-400"></i>
                                </button>
                            </div>
                            
                            <div class="p-6 max-h-[60vh] overflow-y-auto">
                                <div class="grid grid-cols-3 md:grid-cols-4 gap-4" id="albumGrid">
                                    <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-100 group">
                                        <img src="/asset/img/product-1.jpg" class="w-full h-full object-cover">
                                        <button class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-gray-50 flex justify-end gap-3">
                                <button onclick="closeAlbumModal()" class="px-6 py-2.5 rounded-xl text-[14px] font-semibold text-gray-500 hover:bg-gray-200 transition-all">Đóng</button>
                                <button class="px-6 py-2.5 rounded-xl text-[14px] font-semibold bg-[#bc9c75] text-white shadow-lg shadow-[#bc9c75]/20">Thêm ảnh mới</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6 flex justify-end gap-3">
                    <button onclick="loadPage('product-list')" class="px-6 py-2 border border-gray-200 rounded-xl text-xs font-semibold text-gray-500 hover:bg-gray-50">Quay lại</button>
                    <button id="btn-save-product" class="px-6 py-2 bg-[#bc9c75] text-white rounded-xl text-xs font-semibold shadow-sm hover:opacity-90">
                        Lưu sản phẩm
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
