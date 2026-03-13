@extends('layouts.admin-account-manager-layout')
@section('title', 'Admin Profile')
@section('content')

    <div>
        <div class="tab-content active">
            <div class="bg-white rounded-[24px] shadow-sm p-6 md:p-10 max-w-4xl mx-auto border border-gray-100">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Hồ sơ cá nhân</h2>
                    <p class="text-sm text-gray-400">Thông tin này sẽ được hiển thị trên hệ thống quản trị</p>
                </div>

                @livewire('admin.profile')
                
            </div>
        </div>
    </div>

@endsection
