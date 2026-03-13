@extends('layouts.auth-layout')
@section('title', 'Đăng ký')

@section('content')

    <div>
        <div class="w-full max-w-[480px] bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 md:p-10">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Tạo tài khoản</h2>
                <p class="text-gray-400 text-sm mt-2">Tham gia cùng chúng tôi ngay hôm nay!</p>
            </div>

            <form action="{{ route('register_handler') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Tên đăng
                        nhập</label>
                    <div class="relative group">
                        <i
                            class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                        <input type="text" name="username" placeholder="Nhập username" value="{{ old('username') }}"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm">
                    </div>
                    @error('username')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label
                        class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Email</label>
                    <div class="relative group">
                        <i
                            class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                        <input type="email" name="email" placeholder="example@gmail.com" value="{{ old('email') }}"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm"
                            required>
                    </div>
                    @error('email')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Mật
                            khẩu</label>
                        <div class="relative group">
                            <i
                                class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                            <input type="password" name="password" placeholder="••••••••"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm"
                                required>
                        </div>
                        @error('password')
                            <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Xác
                            nhận</label>
                        <div class="relative group">
                            <i
                                class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                            <input type="password" name="password_confirmation" placeholder="••••••••"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm"
                                required>
                        </div>
                        @error('password_confirmation')
                            <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-[#bc9c75] text-white py-4 mt-6 rounded-xl font-bold hover:opacity-90 transition shadow-md shadow-[#bc9c75]/20 active:scale-[0.98]">
                    ĐĂNG KÝ NGAY
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-400 mb-4">Đã có tài khoản?</p>
                <a href="{{ route('login') }}"
                    class="inline-block w-full py-3.5 border border-gray-200 rounded-xl font-bold text-gray-700 hover:bg-gray-50 hover:border-[#bc9c75] hover:text-[#bc9c75] transition text-sm">
                    QUAY LẠI ĐĂNG NHẬP
                </a>
            </div>
        </div>
    </div>

@endsection
