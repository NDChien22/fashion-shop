@extends('layouts.auth-layout')
@section('title', 'Đăng nhập')
@section('content')

    <div
        class="w-full max-w-[420px] bg-white rounded-[1.5rem] sm:rounded-[2rem] shadow-sm border border-gray-100 p-6 sm:p-8 md:p-10">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Đăng nhập</h2>
            <p class="text-gray-400 text-sm mt-2">Chào mừng bạn quay trở lại!</p>
        </div>

        <form action="{{ route('login_handler') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Tên đăng nhập</label>
                <div class="relative group">
                    <i
                        class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                    <input type="text" name="login_id" placeholder="Nhập username" value="{{ old('login_id') }}"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm">
                </div>
                @error('login_id')
                    <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <div class="flex justify-between mb-2 ml-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Mật khẩu</label>

                </div>
                <div class="relative group">
                    <i
                        class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                    <input type="password" name="password" placeholder="••••••••" value="{{ old('password') }}"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm">
                </div>
                @error('password')
                    <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                @enderror
                <div>
                    <a href="{{ route('forgot_password') }}" class="text-xs font-medium text-[#bc9c75] hover:underline">Quên
                        mật khẩu?</a>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#bc9c75] text-white py-4 rounded-xl font-bold hover:opacity-90 transition shadow-md shadow-[#bc9c75]/20 active:scale-[0.98] mt-2">
                ĐĂNG NHẬP
            </button>

            <div class="relative flex items-center py-2">
                <div class="flex-grow border-t border-gray-100"></div>
                <span class="flex-shrink mx-4 text-gray-300 text-[10px] uppercase tracking-[0.2em]">Hoặc</span>
                <div class="flex-grow border-t border-gray-100"></div>
            </div>

            <a href="{{ route('google_login') }}"
                class="w-full flex items-center justify-center gap-3 py-3.5 border border-gray-100 rounded-xl hover:bg-gray-50 transition font-semibold text-gray-600 text-sm">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" class="w-5 h-5"
                    alt="Google">
                Tiếp tục với Google
            </a>
        </form>

        <p class="text-center mt-10 text-sm text-gray-400">
            Chưa có tài khoản? <a href="{{ route('register') }}"
                class="font-bold text-gray-800 hover:text-[#bc9c75] transition">Đăng ký ngay</a>
        </p>
    </div>

@endsection
