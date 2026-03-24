@extends('layouts.admin-layout')
@section('title', 'Quản lý nhân viên')
@section('content')

    <div>
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-[10px]"></i>
                </span>
                <input id="searchInput" placeholder="Tìm theo tên hoặc mã..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2.5 pl-10 pr-4 text-xs focus:outline-none focus:ring-1 focus:ring-[#bc9c75] transition-all">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">

                <button onclick="openForm('employee')"
                    class="px-5 py-2.5 bg-[#bc9c75] text-white rounded-xl text-xs font-bold shadow-md shadow-[#bc9c75]/20 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus text-[10px]"></i> Thêm nhân viên
                </button>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead class="bg-[#fcfaf8] border-b border-gray-50">
                        <tr>
                            <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nhân viên
                            </th>
                            <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">CHức vụ
                            </th>
                            <th
                                class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                Liên hệ</th>
                            <th
                                class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                Ngày gia nhập</th>
                            <th
                                class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-[12px]">
                        <tr class="hover:bg-gray-50/50 transition-all group">
                            <td class="px-6 py-4">
                                <div onclick="openModal('employeeDetailModal')" class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-[#bc9c75]/10 flex items-center justify-center text-[#bc9c75] font-black border-2 border-white shadow-sm">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </div>
                                    <div>
                                        <div class="font-black text-gray-800 uppercase tracking-tighter">Trần Quốc Bảo</div>
                                        <div class="text-[10px] text-gray-400">Mã nhân viên: NV11234 </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 bg-red-50 text-red-500 rounded-lg font-black text-[9px] uppercase tracking-widest border border-red-100">Administrator</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="font-bold text-gray-600">0902.xxx.789</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-gray-400 font-medium">10/01/2026</div>
                            </td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <button onclick="openForm('employee')"
                                    class="text-gray-400 hover:text-[#bc9c75] transition-all">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
