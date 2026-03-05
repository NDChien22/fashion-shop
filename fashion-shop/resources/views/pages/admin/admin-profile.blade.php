@extends('layouts.admin-account-manager-layout')
@section('title', 'Admin Profile')
@section('content')

    <div id="thong-tin" class="tab-content active">
        <div class="profile-section card">
            <div class="profile-header">
                <h2><strong>Hồ sơ cá nhân</strong></h2>
                <p>Quản lý thông tin chi tiết cá nhân của bạn để bảo mật tài khoản</p>
            </div>

            @livewire('admin.profile')

        </div>
    </div>

@endsection
