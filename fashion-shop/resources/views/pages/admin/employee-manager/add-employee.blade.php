@extends('layouts.admin-layout')
@section('title', 'Thêm nhân viên')
@section('content')

    <div>
        <div class="bg-white p-8 rounded-2xl border border-gray-200 max-w-4xl mx-auto shadow-sm">


            <div id="employee-form" class="grid grid-cols-2 gap-5">
                <div>
                    <label class="text-sm text-gray-600 font-medium">Họ và tên</label>
                    <input type="text" id="emp-fullname"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Email</label>
                    <input type="email" id="emp-email"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Số điện thoại</label>
                    <input type="text" id="emp-phone"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Giới tính</label>
                    <select id="emp-gender"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Ngày sinh</label>
                    <input type="date" id="emp-birthday"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Chức vụ</label>
                    <select id="emp-role"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Username</label>
                    <input type="text" id="emp-username"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Mật khẩu</label>
                    <input type="password" id="emp-password"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all"
                        placeholder="Để trống nếu không muốn đổi">
                </div>

                <div class="col-span-2">
                    <label class="text-sm text-gray-600 font-medium">Địa chỉ</label>
                    <input type="text" id="emp-address"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                </div>
            </div>

            <div class="flex justify-between items-center mt-8 border-t pt-5">
                <button onclick="loadPage('hr')"
                    class="text-gray-400 text-sm hover:text-gray-700 font-medium transition-colors">
                    ← Quay lại danh sách
                </button>
                <button id="btn-submit-employee"
                    class="bg-[#bc9c75] text-white px-8 py-2.5 rounded-xl text-sm font-bold hover:shadow-lg hover:shadow-[#bc9c75]/20 transition-all active:scale-95">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>

@endsection
