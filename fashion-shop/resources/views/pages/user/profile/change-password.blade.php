@extends('layouts.user-static-layout')
@section('title', 'Đổi mật khẩu')

@section('main-content')
    <div class="max-w-3xl mx-auto px-4 md:px-6 py-10">
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 md:p-8">
            <div class="mb-6">
                <p class="text-[11px] uppercase tracking-[0.2em] text-[#bc9c75] font-bold">Bảo mật tài khoản</p>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 mt-2">Đổi mật khẩu</h1>
                <p class="text-sm text-gray-500 mt-2">Cập nhật mật khẩu mới để bảo vệ tài khoản của bạn.</p>
            </div>

            <form action="{{ route('user.profile.password.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Mật khẩu hiện
                        tại</label>
                    <input type="password" name="current_password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#bc9c75] focus:bg-white"
                        autocomplete="current-password">
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Mật khẩu
                            mới</label>
                        <input type="password" name="password"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#bc9c75] focus:bg-white"
                            autocomplete="new-password">
                        @error('password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Xác nhận mật khẩu
                            mới</label>
                        <input type="password" name="password_confirmation"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#bc9c75] focus:bg-white"
                            autocomplete="new-password">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('user.profile') }}"
                        class="px-6 py-3 border border-gray-200 text-gray-600 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-gray-50 transition">
                        Quay lại hồ sơ
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-gray-900 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-black transition">
                        Cập nhật mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
