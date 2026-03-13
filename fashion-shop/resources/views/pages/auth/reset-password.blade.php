@extends('layouts.auth-layout')
@section('title', 'Đặt lại mật khẩu')
@section('content')
    @if (session('toast') || session('success') || session('error'))
        <x-toast :message="session('toast')" :success="session('success')" :error="session('error')" />
    @endif

    <div>
        <div class="w-full max-w-[420px] bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 md:p-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Đổi mật khẩu</h2>
            <p class="text-gray-400 text-sm mb-8">Vui lòng nhập mật khẩu mới cho tài khoản của bạn.</p>

            <form action="{{ route('reset_password')}}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Mật khẩu
                        mới</label>
                    <div class="relative group">
                        <i
                            class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                        <input type="password" name="password" placeholder="Nhập mật khẩu mới"
                            class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm">
                    </div>
                    @error('password')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Xác nhận mật
                        khẩu</label>
                    <div class="relative group">
                        <i
                            class="fa-solid fa-check-double absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                        <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu"
                            class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm">
                    </div>
                    @error('password_confirmation')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-[#bc9c75] text-white py-4 rounded-xl font-bold hover:opacity-90 transition shadow-md shadow-[#bc9c75]/20 active:scale-[0.98] mt-4">
                    CẬP NHẬT MẬT KHẨU
                </button>

                <div class="text-center mt-6">
                    <a href="{{ route('login') }}"
                        class="text-xs font-bold text-gray-400 hover:text-red-500 tracking-widest transition uppercase">
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
