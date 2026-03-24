@extends('layouts.admin-layout')
@section('title', 'Danh sách đơn hàng')
@section('content')

    <div>
        <div class=" mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex gap-4 flex-1">
                <div class="relative w-full max-w-[50%]">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Tìm kiếm đơn hàng..."
                        class="w-full bg-white border border-gray-100 rounded-xl py-2.5 pl-10 pr-4 text-xs focus:ring-1 focus:ring-[#bc9c75] outline-none shadow-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
            <table class="w-full  text-left border-collapse">
                <thead class="bg-[#fcfaf8] border-b border-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mã đơn / Ngày
                        </th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Khách hàng</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Thanh toán</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Tổng (Final)</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Trạng thái</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-[12px]">
                    <tr class="hover:bg-gray-50/50 transition-all">
                        <td class="px-6 py-4">
                            <div class="font-mono font-bold text-gray-800 uppercase">#ORD-99283</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">12/05/2026 14:30</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-700">Lê Minh Anh</div>
                            <div class="text-[10px] text-gray-400">0982 xxx 888</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-credit-card text-gray-300"></i>
                                <span class="font-medium text-gray-600">Banking</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="font-black text-[#bc9c75]">1.450.000đ</div>
                            <div class="text-[9px] text-red-400 font-bold line-through">1.600.000đ</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-2.5 py-1 bg-blue-50 text-blue-500 rounded-lg font-black text-[9px] uppercase tracking-tighter">Chờ
                                xử lý</span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">

                                <button class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition"
                                    title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition"
                                    title="In hóa đơn">
                                    <i class="fas fa-print"></i>
                                </button>

                                <button onclick=""
                                    class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition"
                                    title="Xác nhận đơn">
                                    <i class="fas fa-check"></i>
                                </button>

                                <button onclick=""
                                    class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition"
                                    title="Từ chối đơn">
                                    <i class="fas fa-times"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
