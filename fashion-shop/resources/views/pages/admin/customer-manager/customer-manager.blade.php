@extends('layouts.admin-layout')
@section('title', 'Danh sách khách hàng')
@section('content')

    <div>
        <div class="flex items-center justify-between mb-6">
            <div class="flex gap-4 flex-1">
                <div class="relative w-full max-w-[50%]">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Tìm kiếm khách hàng..."
                        class="w-full bg-white border border-gray-100 rounded-xl py-2.5 pl-10 pr-4 text-xs focus:ring-1 focus:ring-[#bc9c75] outline-none shadow-sm">
                </div>

            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
            <table class="w-full border-collapse">
                <thead
                    class="bg-[#fcfaf8] border-b border-gray-50 text-gray-400 font-bold uppercase tracking-wider text-[10px]">
                    <tr class="border-b border-gray-50">
                        <th class="text-left py-5 px-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Khách
                            hàng</th>
                        <th class="text-left py-5 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Hạng
                        </th>
                        <th class="text-left py-5 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Liên
                            hệ</th>
                        <th class="text-center py-5 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest ">
                            Đơn hàng</th>
                        <th class="text-right py-5 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tổng
                            chi tiêu</th>
                        <th class="py-5 px-8"></th>
                    </tr>
                </thead>
                <tbody id="customer-list-tbody">
                    <tr class="border-b border-gray-50 hover:bg-[#fffbf7]/50 transition-all group">
                        <td onclick="openModal('customerDetailModal')" class="py-4 px-8">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-11 h-11 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-100 group-hover:border-[#bc9c75]/30 overflow-hidden">
                                    <i class="fa-solid fa-user-tie text-[#bc9c75] text-base"></i>
                                </div>
                                <div>
                                    <h4 class="text-[13px] font-black text-gray-800 uppercase tracking-tight">Nguyễn Hoàng
                                        Nam</h4>
                                    <p class="text-[10px] text-gray-400 font-medium italic">Mã khách hàng: XQ2141</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span
                                class="bg-[#fffbf2] text-[#bc9c75] border border-[#bc9c75]/20 px-2.5 py-1 rounded-lg text-[8px] font-black uppercase tracking-wider">
                                Gold Member
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-[11px] text-gray-500 font-bold">0912.xxx.456</div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="text-[12px] font-black text-gray-700">24 <span
                                    class="text-[9px] font-medium text-gray-400 ml-0.5">đơn</span></div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="text-[13px] font-black text-[#bc9c75]">12.850.000đ</div>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
