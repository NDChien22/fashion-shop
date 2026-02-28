@extends('layouts.auth-layout')
@section('title', 'Đặt lại mật khẩu')
@section('content')
    @if (session('toast') || session('success') || session('error'))
        <x-toast :message="session('toast')" />
    @endif

    <div class="login-wrapper reset-password">
        <div class="auth-login-container">
            <h2 class="auth-login-title">Đặt lại mật khẩu</h2>

            <form id="changePasswordForm" action="{{ route('reset_password') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="auth-input-group">
                    <label>Mật khẩu mới</label>
                    <div class="auth-input-with-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Nhập mật khẩu mới" required>
                    </div>
                    @error('password')
                        <span class="text-danger ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-input-group mt-3">
                    <label>Nhập lại mật khẩu</label>
                    <div class="auth-input-with-icon">
                        <i class="fa-solid fa-check-double"></i>
                        <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                    </div>
                    @error('password_confirmation')
                        <span class="text-danger ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-login">CẬP NHẬT MẬT KHẨU</button>

                <div class="signup-footer">
                    <a href="{{ route('login') }}" class="btn-login-outline">Quay lại đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
@endsection
