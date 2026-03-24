@extends('layouts.admin-layout')
@section('title', 'Chương trình khuyến mãi')
@section('content')

    <div>
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase leading-none mb-1">Đang chạy</p>
                        <p class="text-lg font-bold">03 Chiến dịch</p>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#fffbf7] text-[#bc9c75] rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase leading-none mb-1">Mã giảm giá</p>
                        <p class="text-lg font-bold">12 Voucher</p>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button onclick="openForm('promotion')"
                        class="bg-[#bc9c75] text-white px-6 py-2.5 rounded-[18px] text-[13px] font-bold shadow-md shadow-[#bc9c75]/20 hover:brightness-95 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tạo khuyến mãi mới
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">Danh sách chương trình</h3>
                    <div class="flex gap-2">
                        <span
                            class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[10px] font-bold cursor-pointer">Tất
                            cả</span>
                        <span
                            class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-bold cursor-pointer">Đang
                            diễn ra</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Chương trình
                                </th>
                                <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Mức giảm</th>
                                <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Thời gian</th>
                                <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Trạng thái</th>
                                <th class="p-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Thao
                                    tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-tags"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">Sale chào hè 2026</p>
                                            <p class="text-[10px] text-gray-400">Áp dụng cho toàn bộ váy nữ</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="font-bold text-red-500">-20%</span>
                                </td>
                                <td class="p-4">
                                    <div class="text-[11px] leading-tight">
                                        <p class="text-gray-600">Bắt đầu: 01/06/2026</p>
                                        <p class="text-gray-400">Kết thúc: 15/06/2026</p>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-green-50 text-green-600 rounded text-[10px] font-bold">ĐANG
                                        CHẠY</span>
                                </td>
                                <td class="p-4 text-right">
                                    <button onclick="openForm('promotion')"
                                        class="text-gray-400 hover:text-[#bc9c75] mx-1"><i
                                            class="fa-solid fa-pen-to-square "></i></button>
                                    <button class="text-gray-400 hover:text-red-500 mx-1"><i
                                            class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50/50 transition-colors text-gray-400">
                                <td class="p-4">
                                    <div class="flex items-center gap-3 opacity-60">
                                        <div
                                            class="w-10 h-10 bg-gray-100 text-gray-500 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm">Xả kho mùa đông</p>
                                            <p class="text-[10px]">Đã kết thúc vào tháng trước</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-bold">-50%</td>
                                <td class="p-4 text-[11px]">31/12/2025</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-400 rounded text-[10px] font-bold">HẾT
                                        HẠN</span>
                                </td>
                                <td class="p-4 text-right text-gray-300">
                                    <button class="mx-1"><i class="fa-solid fa-eye"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
