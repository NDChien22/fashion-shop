@extends('layouts.admin-layout')
@section('title', 'Thêm flash sale')
@section('content')

    <div>
        <div
            class="max-w-6xl mx-auto my-10 p-8 bg-white rounded-[40px] shadow-2xl shadow-gray-200/50 animate-fade-in border border-gray-50">


            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-gray-50/80 p-6 rounded-4xl border border-gray-100">
                        <p
                            class="text-[10px] font-black text-[#bc9c75] uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span
                                class="w-5 h-5 rounded-full bg-[#bc9c75] text-white flex items-center justify-center text-[8px]">1</span>
                            Cấu hình ưu đãi
                        </p>

                        <div class="space-y-4">
                            <div class="relative group">
                                <input type="number" id="promo-value" placeholder="0"
                                    class="w-full pl-6 pr-20 py-4 bg-white border-2 border-transparent rounded-2xl shadow-sm focus:border-[#bc9c75]/30 outline-none font-black text-3xl text-red-500 transition-all">
                                <select id="promo-type"
                                    class="absolute right-3 top-2.5 bottom-2.5 bg-gray-50 border-none rounded-xl text-xs font-black px-3 cursor-pointer">
                                    <option value="percent">%</option>
                                    <option value="amount">đ</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Bắt đầu</label>
                                    <input type="datetime-local" id="promo-start"
                                        class="w-full px-4 py-3 bg-white border-none rounded-xl text-[11px] font-bold text-gray-600 shadow-sm outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Kết thúc</label>
                                    <input type="datetime-local" id="promo-end"
                                        class="w-full px-4 py-3 bg-white border-none rounded-xl text-[11px] font-bold text-gray-600 shadow-sm outline-none">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Giới hạn số lượng
                                    bán</label>
                                <input type="number" id="promo-limit" placeholder="Nhập số lượng..."
                                    class="w-full px-4 py-3 bg-white border-none rounded-xl shadow-sm outline-none text-sm font-bold">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 space-y-6">
                    <p class="text-[10px] font-black text-[#bc9c75] uppercase tracking-widest flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded-full bg-[#bc9c75] text-white flex items-center justify-center text-[8px]">2</span>
                        Đối tượng áp dụng
                    </p>

                    <div class="grid grid-cols-3 gap-3">
                        <button onclick="selectType('product')" id="card-product"
                            class="apply-card p-4 bg-gray-50 rounded-3xl border-2 border-transparent transition-all flex flex-col items-center gap-2 group hover:bg-white hover:shadow-lg">
                            <div
                                class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-300 group-hover:text-[#bc9c75] transition-all">
                                <i class="fa-solid fa-tag text-sm"></i>
                            </div>
                            <span class="font-bold text-[9px] uppercase text-gray-400 group-hover:text-gray-700">Sản
                                phẩm</span>
                        </button>

                        <button onclick="selectType('category')" id="card-category"
                            class="apply-card p-4 bg-gray-50 rounded-3xl border-2 border-transparent transition-all flex flex-col items-center gap-2 group hover:bg-white hover:shadow-lg">
                            <div
                                class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-300 group-hover:text-[#bc9c75] transition-all">
                                <i class="fa-solid fa-folder-tree text-sm"></i>
                            </div>
                            <span class="font-bold text-[9px] uppercase text-gray-400 group-hover:text-gray-700">Danh
                                mục</span>
                        </button>

                        <button onclick="selectType('collection')" id="card-collection"
                            class="apply-card p-4 bg-gray-50 rounded-3xl border-2 border-transparent transition-all flex flex-col items-center gap-2 group hover:bg-white hover:shadow-lg">
                            <div
                                class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-300 group-hover:text-[#bc9c75] transition-all">
                                <i class="fa-solid fa-layer-group text-sm"></i>
                            </div>
                            <span class="font-bold text-[9px] uppercase text-gray-400 group-hover:text-gray-700">Bộ sưu
                                tập</span>
                        </button>
                    </div>

                    <div id="detail-selection-area"
                        class="bg-gray-50/50 rounded-3xl p-5 border border-gray-100 hidden animate-fade-in">
                        <div id="detail-list"
                            class="grid grid-cols-1 gap-2 max-h-[200px] overflow-y-auto pr-2 custom-scrollbar">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-10 pt-6 border-t border-gray-50">
                <button onclick="loadPage('flash-sale')"
                    class="px-8 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-gray-200 transition-all">
                    Hủy bỏ
                </button>
                <button id="btn-save-promotion"
                    class="px-10 py-3 bg-[#bc9c75] text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-[#bc9c75]/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Xác nhận tạo
                </button>
            </div>
        </div>
    </div>

@endsection
