@extends('layouts.admin-account-manager-layout')
@section('title', 'Admin Account Manager')
@section('content')

    <div id="tai-khoan" class="tab-content active">
        <div class="account-card">
            <div class="account-header" style="text-align: center;">
                <h2>Cài đặt tài khoản & Bảo mật</h2>
                <p>Cập nhật mật khẩu và quản lý các thiết lập an toàn cho tài khoản của bạn</p>
            </div>

            @livewire('admin.account-manager')

            <a href="index.html" class="btn-back-square">
                <i class="fa-solid fa-chevron-left"></i>
                <span>Quay lại </span>
            </a>
        </div>
    </div>

@endsection
