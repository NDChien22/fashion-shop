@extends('layouts.admin-layout')
@section('title', 'Voucher')
@section('content')

    <div>
        <div class="mb-6 flex justify-between items-center">
            <div class="relative w-64">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                <input type="text" placeholder="Tìm mã voucher..."
                    class="w-full bg-white border border-gray-100 rounded-xl py-2 pl-9 pr-4 text-xs focus:ring-1 focus:ring-[#bc9c75] outline-none shadow-sm">
            </div>
            <button onclick="openForm('voucher')"
                class="px-4 py-2 bg-[#bc9c75] text-white rounded-xl text-xs font-semibold shadow-sm hover:opacity-90 transition">
                <i class="fa-solid fa-plus mr-2"></i> Tạo mã mới
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-50 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-16 h-16 bg-[#bc9c75]/10 rounded-bl-full flex items-center justify-center">
                    <i class="fa-solid fa-ticket text-[#bc9c75]"></i>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-black text-[#bc9c75] uppercase tracking-wider">CODE: SUMMER2026</span>
                    <h3 class="text-base font-bold text-gray-800">Giảm 10%</h3>
                    <p class="text-[11px] text-gray-400">Đơn tối thiểu: 500.000đ</p>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-50 space-y-3">
                    <div class="flex justify-between text-[10px]">
                        <span class="text-gray-400 uppercase font-bold tracking-tighter">Tiến độ: 45/100</span>
                        <span class="text-gray-500 font-bold tracking-tighter">Hết hạn: 30/08/2026</span>
                    </div>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#bc9c75] h-full" style="width: 45%"></div>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <span
                        class="text-[9px] text-green-500 font-bold bg-green-50 px-2 py-1 rounded-full border border-green-100 uppercase">Hoạt
                        động</span>

                    <div class="flex items-center gap-2">
                        <button onclick="openForm('voucher')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-[#bc9c75]/10 hover:text-[#bc9c75] transition-all">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </button>
                        <button
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
