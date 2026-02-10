@extends('layouts.auth-layout')
@section('title', 'Đăng ký')

@section('content')

    <div class="auth-login-wrapper">
        <div class="auth-login-container">
            <h2 class="login-title">Đăng ký</h2>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="auth-input-group mt-3">
                    <label>Tên đăng nhập</label>
                    <div class="auth-input-with-icon">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="username" placeholder="Nhập tên đăng nhập" value="{{old('username')}}">
                    </div>
                    @error('username')
                        <span class="text-danger ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-input-group mt-3">
                    <label>Email</label>
                    <div class="auth-input-with-icon">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" placeholder="Nhập email">
                    </div>
                    @error('email')
                        <span class="text-danger ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-input-group mt-3">
                    <label>Mật khẩu</label>
                    <div class="auth-input-with-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Nhập mật khẩu">
                    </div>
                    @error('password')
                        <span class="text-danger ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-input-group mt-3">
                    <label>Xác nhận mật khẩu</label>
                    <div class="auth-input-with-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu">
                    </div>
                    @error('password_confirmation')
                        <span class="text-danger ml-1">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-login">ĐĂNG KÝ</button>

                <div class="signup-footer">
                    <p>Bạn đã có tài khoản?</p>
                    <a href="{{ route('login') }}" class="btn-login-outline">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>

@endsection
