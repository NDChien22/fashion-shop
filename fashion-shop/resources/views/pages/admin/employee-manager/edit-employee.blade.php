@extends('layouts.admin-layout')
@section('title', 'Chỉnh sửa nhân viên')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Quản lý nhân viên
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <a href="{{ route('admin.employee-manager') }}" class="cursor-pointer hover:text-[#bc9c75] transition">
            Quản lý nhân viên
        </a>
        / <span class="cursor-pointer hover:text-[#bc9c75] transition">Chỉnh sửa nhân viên</span>
        / <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">{{ $employee->employee_code }}</span>
    </p>
@endsection

@section('content')

    <div>
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $user = $employee->user;
            $selectedRole = match (strtolower((string) ($user->role ?? ''))) {
                'admin' => 'admin',
                'productmanager', 'manager' => 'productmanager',
                'servicescustomer', 'staff' => 'servicescustomer',
                default => old('role', 'admin'),
            };
            $formRole = strtolower((string) old('role', $selectedRole));
            $birthdayDisplay = old('birthday', $user->birthday?->format('Y-m-d'));
            $hireDateDisplay = old('hire_date', $employee->hire_date?->format('Y-m-d'));
        @endphp

        <form action="{{ route('admin.update-employee', $employee) }}" method="POST"
            class="bg-white p-8 rounded-2xl border border-gray-200 max-w-4xl mx-auto shadow-sm">
            @csrf
            @method('PUT')

            <div id="employee-form" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm text-gray-600 font-medium">Mã nhân viên</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}"
                        id="emp-code" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                    <p class="mt-1 text-[11px] text-gray-400">Mã nhân viên được sinh tự động theo chức vụ và không thể chỉnh
                        sửa.</p>
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Họ và tên</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                        id="emp-fullname" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" id="emp-email" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Số điện thoại</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                        id="emp-phone" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Giới tính</label>
                    <input type="hidden" name="gender" value="{{ old('gender', $user->gender) }}">
                    <input type="text"
                        value="{{ match (old('gender', $user->gender)) {
                            'female' => 'Nữ',
                            'other' => 'Khác',
                            default => 'Nam',
                        } }}"
                        readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Ngày sinh</label>
                    <input type="hidden" name="birthday" value="{{ $birthdayDisplay }}">
                    <input type="text" value="{{ $user->birthday?->format('d/m/Y') ?? '' }}" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Chức vụ</label>
                    <select id="emp-role" name="role"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                        <option value="admin" {{ $formRole === 'admin' ? 'selected' : '' }}>Admin
                        </option>
                        <option value="productmanager" {{ $formRole === 'productmanager' ? 'selected' : '' }}>
                            Product Manager
                        </option>
                        <option value="servicescustomer" {{ $formRole === 'servicescustomer' ? 'selected' : '' }}>
                            Services Customer
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" id="emp-username"
                        readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Lương</label>
                    <input type="number" min="0" step="0.01" name="salary"
                        value="{{ old('salary', $employee->salary) }}" id="emp-salary"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Ngày vào làm</label>
                    <input type="hidden" name="hire_date" value="{{ $hireDateDisplay }}">
                    <input type="text" value="{{ $employee->hire_date?->format('d/m/Y') ?? '' }}" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div class="col-span-2">
                    <label class="text-sm text-gray-600 font-medium">Địa chỉ</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}" id="emp-address"
                        readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>
            </div>

            <div class="flex justify-between items-center mt-8 border-t pt-5">
                <a href="{{ route('admin.employee-manager') }}"
                    class="text-gray-400 text-sm hover:text-gray-700 font-medium transition-colors">
                    ← Quay lại danh sách
                </a>
                <button type="submit" id="btn-submit-employee"
                    class="bg-[#bc9c75] text-white px-8 py-2.5 rounded-xl text-sm font-bold hover:shadow-lg hover:shadow-[#bc9c75]/20 transition-all active:scale-95">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

@endsection
