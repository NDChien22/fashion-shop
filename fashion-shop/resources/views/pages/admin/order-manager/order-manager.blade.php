@extends('layouts.admin-layout')
@section('title', 'Quản lý đơn hàng')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Quản lý đơn hàng
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Trang chính
        </span>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Đơn hàng</span>
    </p>
@endsection

@section('content')
    <div>
        @livewire('admin.order-manager')
    </div>
@endsection
