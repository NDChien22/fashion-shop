@extends('layouts.admin-layout')
@section('title', 'Thêm mã giảm giá')
@section('content')

    <div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-3xl border border-gray-50 shadow-sm space-y-8">

                    <div>
                        <h4 class="text-[11px] font-black text-[#bc9c75] uppercase mb-5 flex items-center gap-2">
                            Thông tin cơ bản
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Mã giảm giá
                                    (Code)</label>
                                <input type="text" placeholder="Ví dụ: QUY2_2026"
                                    class="w-full mt-2 bg-gray-50 border-none rounded-2xl py-3.5 px-5 text-xs font-mono font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none uppercase placeholder:text-gray-300">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Loại chiết
                                    khấu (Type)</label>
                                <select
                                    class="w-full mt-2 bg-gray-50 border-none rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none appearance-none cursor-pointer">
                                    <option value="fixed">Số tiền cố định (VNĐ)</option>
                                    <option value="percent">Phần trăm (%)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-50">
                        <h4 class="text-[11px] font-black text-[#bc9c75] uppercase mb-5 flex items-center gap-2">
                            Giá trị & Hạn mức
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Mức giảm
                                    (Value)</label>
                                <div class="relative mt-2">
                                    <input type="number" placeholder="0"
                                        class="w-full bg-gray-50 border-none rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                                    <span
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300 uppercase">Đơn
                                        vị</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Đơn tối
                                    thiểu (Min Order)</label>
                                <input type="number" placeholder="0"
                                    class="w-full mt-2 bg-gray-50 border-none rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Giảm tối
                                    đa (Max Discount)</label>
                                <input type="number" placeholder="Không giới hạn"
                                    class="w-full mt-2 bg-gray-50 border-none rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-50">
                        <h4 class="text-[11px] font-black text-[#bc9c75] uppercase mb-5 flex items-center gap-2">
                            Loại sản phẩm
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Bộ sưu
                                    tập</label>
                                <select id="prod-cate" class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                    <option value="">-- Không chọn --</option>
                                    <option>...</option>
                                    <option>...</option>
                                    <option>...</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Danh
                                    mục</label>
                                <select id="prod-cate" class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs">
                                    <option value="">-- Không chọn --</option>
                                    <option>Áo khoác</option>
                                    <option>Áo thun</option>
                                    <option>Quần</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-50">
                        <h4 class="text-[11px] font-black text-[#bc9c75] uppercase mb-5 flex items-center gap-2">
                            Thời gian & Giới hạn
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Ngày bắt
                                    đầu</label>
                                <input type="date"
                                    class="w-full mt-2 bg-gray-50 border-none rounded-2xl py-3.5 px-5 text-[11px] font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Ngày kết
                                    thúc</label>
                                <input type="date"
                                    class="w-full mt-2 bg-gray-50 border-none rounded-2xl py-3.5 px-5 text-[11px] font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Số lượng
                                    phát hành</label>
                                <input type="number" placeholder="Ví dụ: 100"
                                    class="w-full mt-2 bg-gray-50 border-none rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-gray-50 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">Trạng thái (Active)</h3>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="v-status" class="sr-only peer" checked>
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#bc9c75]">
                            </div>
                        </label>
                    </div>
                    <p class="text-[10px] text-gray-400 font-medium leading-relaxed">Khi kích hoạt, khách hàng có thể áp
                        dụng mã này ngay lập tức nếu thỏa mãn điều kiện.</p>
                </div>

                <div
                    class="bg-[#bc9c75] p-6 rounded-4xl shadow-xl shadow-[#bc9c75]/20 relative overflow-hidden text-white min-h-[180px] flex flex-col justify-between">
                    <i class="fa-solid fa-ticket absolute -right-6 -top-6 text-white/10 text-9xl rotate-12"></i>

                    <div class="relative z-10">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] px-2 py-1 bg-white/20 rounded">Preview
                            Voucher</span>
                        <h4 class="text-2xl font-black mt-4 uppercase tracking-tighter" id="pre-val">Giảm --%</h4>
                        <p class="text-[10px] opacity-80 mt-1 font-medium" id="pre-min">Cho đơn hàng từ 0đ</p>
                    </div>

                    <div class="relative z-10 pt-4 border-t border-white/20 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] font-bold opacity-60 uppercase">Mã: <span id="pre-code"
                                    class="text-white ml-1">CHƯA NHẬP</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] opacity-60 uppercase font-bold text-white/50 italic">Hết hạn: --/--/----
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50/50 border border-blue-100/50 p-6 rounded-3xl">
                    <div class="flex items-center gap-3 mb-3 text-blue-500">
                        <i class="fa-solid fa-circle-info text-sm"></i>
                        <span class="text-[11px] font-black uppercase tracking-wider">Lưu ý</span>
                    </div>
                    <ul class="text-[10px] text-blue-400 space-y-2 font-medium">
                        <li class="flex gap-2">• <span class="flex-1">Trường "Giảm tối đa" chỉ áp dụng khi chọn loại "Phần
                                trăm".</span></li>
                        <li class="flex gap-2">• <span class="flex-1">Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày hiện tại để
                                voucher có hiệu lực ngay.</span></li>
                    </ul>
                </div>

                <button onclick="loadPage('vouchers')"
                    class="px-6 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 transition">Hủy
                    bỏ</button>
                <button id="btn-save-voucher"
                    class="px-6 py-2.5 bg-[#bc9c75] text-white rounded-xl text-xs font-bold shadow-md shadow-[#bc9c75]/20 hover:opacity-90 transition">Tạo
                    Voucher</button>
            </div>
        </div>
    </div>

@endsection
