@extends('layouts.auth-layout')
@section('title', 'Đăng ký')

@section('content')

    <div class="login-wrapper">
        <div class="login-container">
            <h2 class="login-title">Đăng ký</h2>

            <form id="signupForm">
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <div class="input-with-icon">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" placeholder="Nhập tên đăng nhập" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <div class="input-with-icon">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" placeholder="Nhập email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Mật khẩu</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" placeholder="Nhập mật khẩu" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Xác nhận mật khẩu</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" placeholder="Nhập lại mật khẩu" required>
                    </div>
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
