@extends('layouts.auth-layout')
@section('title', 'Quên mật khẩu')
@section('content')
    <div>
        <div class="w-full max-w-[420px] bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 md:p-10 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Quên mật khẩu?</h2>
            <p class="text-gray-400 text-sm mb-8 px-2">
                Nhập email của bạn và chúng tôi sẽ gửi liên kết để đặt lại mật khẩu mới.
            </p>

            <form action="{{ route('send_reset_password_email') }}" method="POST" class="space-y-6">
                @csrf
                <div class="text-left">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Địa chỉ
                        Email</label>
                    <div class="relative group">
                        <i
                            class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#bc9c75] transition-colors"></i>
                        <input type="email" name="email" placeholder="example@gmail.com"
                            class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:border-[#bc9c75] focus:ring-1 focus:ring-[#bc9c75] transition-all text-sm">
                    </div>
                    @error('email')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-[#bc9c75] text-white py-4 rounded-xl font-bold hover:opacity-90 transition shadow-md shadow-[#bc9c75]/20 active:scale-[0.98]">
                    GỬI LIÊN KẾT ĐẶT LẠI
                </button>

                <div class="pt-4">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-[#bc9c75] transition group">
                        <i class="fa-solid fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                        Quay lại Đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
