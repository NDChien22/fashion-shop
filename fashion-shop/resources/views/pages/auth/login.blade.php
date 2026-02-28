@extends('layouts.auth-layout')
@section('title', 'Đăng nhập')
@section('content')

    @if (session('toast') || session('success') || session('error'))
        <x-toast :message="session('toast')" />
    @endif

    <div class="auth-login-container">
        <h2 class="auth-login-title">Đăng nhập</h2>

        <form action="{{ route('login_handler') }}" method="POST">
            @csrf
            <div class="auth-input-group">
                <label>Tên đăng nhập</label>
                <div class="auth-input-with-icon">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" placeholder="Nhập Username hoặc Email" name="login_id" value="{{ old('login_id') }}">
                </div>
            </div>
            @error('login_id')
                <span class="text-danger ml-1">{{ $message }}</span>
            @enderror

            <div class="auth-input-group mt-3">
                <label>Mật khẩu</label>
                <div class="auth-input-with-icon">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" placeholder="Nhập mật khẩu" name="password" value="{{ old('password') }}">
                </div>
            </div>
            @error('password')
                <span class="text-danger ml-1">{{ $message }}</span>
            @enderror
            <div class="forgot-pass">
                <a href="{{ route('forgot_password') }}">Quên mật khẩu?</a>
            </div>


            <button type="submit" class="btn-login">ĐĂNG NHẬP</button>

            <div class="social-section">
                <p class="divider-text">hoặc tiếp tục với</p>
                <div class="social-icons">
                    <a href="{{ route('google_login') }}" class="social-btn google-btn">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
                            alt="Google">
                        Google
                    </a>
                </div>
            </div>

            <div class="signup-section">
                <p>Bạn chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
            </div>
        </form>
    </div>

@endsection
