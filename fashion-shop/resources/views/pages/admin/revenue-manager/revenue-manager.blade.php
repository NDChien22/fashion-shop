@extends('layouts.admin-layout')
@section('title', 'Báo cáo doanh thu')
@section('content')

    <div>
        <div class="mb-6 flex justify-between items-center">
            <div class="flex gap-2 bg-white p-1 rounded-xl border border-gray-100 shadow-sm">
                <button
                    class="px-4 py-1.5 bg-[#fcfaf8] text-[#bc9c75] rounded-lg text-[10px] font-bold uppercase">Tuần</button>
                <button class="px-4 py-1.5 text-gray-400 rounded-lg text-[10px] font-bold uppercase">Tháng</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-50 shadow-sm relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 text-4xl"><i class="fa-solid fa-coins"></i></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Doanh thu thuần</p>
                <h3 class="text-2xl font-black text-gray-800 tracking-tighter">1.248M</h3>
                <p class="text-[10px] text-green-500 font-bold mt-2 flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> +8.4%
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-50 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tổng giảm giá (Voucher)</p>
                <h3 class="text-2xl font-black text-red-400 tracking-tighter">-45.2M</h3>
                <div class="w-full bg-gray-100 h-1 rounded-full mt-3 overflow-hidden">
                    <div class="bg-red-400 h-full shadow-[0_0_8px_rgba(248,113,113,0.4)]" style="width: 35%"></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-50 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Giá vốn ước tính</p>
                <h3 class="text-2xl font-black text-gray-800 tracking-tighter">682.0M</h3>
                <p class="text-[10px] text-gray-400 font-medium mt-2 italic">Dựa trên SKU base_price</p>
            </div>

            <div class="bg-[#bc9c75] p-6 rounded-2xl shadow-xl shadow-[#bc9c75]/20 text-white">
                <p class="text-[10px] font-black opacity-80 uppercase tracking-widest mb-1">Lợi nhuận gộp</p>
                <h3 class="text-2xl font-black tracking-tighter">520.8M</h3>
                <p class="text-[10px] font-bold mt-2 bg-white/20 inline-block px-2 py-0.5 rounded italic">~41.7% Biên lợi
                    nhuận</p>
            </div>
        </div>

        <div class="bg-white p-7 rounded-3xl border border-gray-50 shadow-sm">
            <div class="flex justify-between items-center mb-8">
                <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest">Biểu đồ tăng trưởng đơn hàng</h4>
                <i class="fa-solid fa-ellipsis-vertical text-gray-300 cursor-pointer"></i>
            </div>
            <div class="flex items-end justify-between h-48 gap-4 px-4">
                <div class="flex-1 bg-gray-50 rounded-t-lg relative group h-[40%]">
                    <div
                        class="absolute -top-6 left-1/2 -translate-x-1/2 text-[9px] font-bold text-gray-400 opacity-0 group-hover:opacity-100 transition-all">
                        400</div>
                    <div
                        class="absolute inset-x-0 bottom-0 bg-[#bc9c75]/20 group-hover:bg-[#bc9c75] transition-all rounded-t-lg h-full">
                    </div>
                </div>
                <div class="flex-1 bg-gray-50 rounded-t-lg relative group h-[65%]">
                    <div
                        class="absolute inset-x-0 bottom-0 bg-[#bc9c75]/20 group-hover:bg-[#bc9c75] transition-all rounded-t-lg h-full shadow-[0_0_15px_rgba(188,156,117,0.3)]">
                    </div>
                </div>
                <div class="flex-1 bg-gray-50 rounded-t-lg relative group h-[90%]">
                    <div
                        class="absolute inset-x-0 bottom-0 bg-[#bc9c75] transition-all rounded-t-lg h-full shadow-[0_0_15px_rgba(188,156,117,0.3)]">
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                <span>Tháng 1</span><span>Tháng 2</span><span>Tháng 3</span>
            </div>
        </div>
    </div>

@endsection
