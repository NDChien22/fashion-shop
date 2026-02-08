@extends('layouts.auth-layout')
@section('title', 'Đăng nhập')
@section('content')

    <div class="login-wrapper">
        <div class="login-container">
            <h2 class="login-title">Đăng nhập</h2>

            <form id="loginForm">
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <div class="input-with-icon">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" placeholder="Nhập Username hoặc Email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Mật khẩu</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" placeholder="Nhập mật khẩu" required>
                    </div>
                    <div class="forgot-pass">
                        <a href="{{ route('forgot-password') }}">Quên mật khẩu?</a>
                    </div>
                </div>

                <button type="submit" class="btn-login">ĐĂNG NHẬP</button>

                <div class="social-section">
                    <p class="divider-text">hoặc tiếp tục với</p>
                    <div class="social-icons">
                        <button class="social-btn google-btn">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
                                alt="Google">
                            Google
                        </button>
                    </div>
                </div>

                <div class="signup-section">
                    <p>Bạn chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
                </div>
            </form>
        </div>
    </div>

@endsection

