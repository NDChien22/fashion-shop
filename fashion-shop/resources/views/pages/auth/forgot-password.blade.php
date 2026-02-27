@extends('layouts.auth-layout')
@section('title', 'Quên mật khẩu')
@section('content')
    <div class="login-wrapper">
        <div class="auth-login-container">
            <h2 class="auth-login-title">Đặt lại mật khẩu</h2>
            <p style="text-align: center; color: #666; font-size: 13px; margin-bottom: 25px;">
                Nhập email của bạn và chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu.
            </p>

            <form id="forgotForm">
                <div class="auth-input-group">
                    <label>Email</label>
                    <div class="auth-input-with-icon">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" placeholder="Nhập email của bạn" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">GỬI LIÊN KẾT ĐẶT LẠI</button>



                <div class="signup-footer">
                    <a href="{{ route('login') }}" class="btn-login-outline">Quay lại đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
@endsection
