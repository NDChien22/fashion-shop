@extends('layouts.admin-layout')
@section('title', 'Thêm mã giảm giá')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Thêm mã giảm giá
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <a href="{{ route('admin.voucher-manager') }}" class="cursor-pointer hover:text-[#bc9c75] transition">
            Quản lý mã giảm giá
        </a>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Thêm mới</span>
    </p>
@endsection

@section('content')
    @include('pages.admin.voucher-manager._voucher-form')

@endsection
