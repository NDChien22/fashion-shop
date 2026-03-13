@extends('layouts.admin-account-manager-layout')
@section('title', 'Admin Account Manager')
@section('content')

    <div>
        <div class="tab-content active">
            <div class="bg-white rounded-[24px] shadow-sm p-6 md:p-10 max-w-xl mx-auto border border-gray-100">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Đổi mật khẩu</h2>
                    <p class="text-sm text-gray-400 mt-1">Sử dụng mật khẩu mạnh để bảo vệ tài khoản</p>
                </div>

                @livewire('admin.account-manager')
                
            </div>
        </div>
    </div>

@endsection
