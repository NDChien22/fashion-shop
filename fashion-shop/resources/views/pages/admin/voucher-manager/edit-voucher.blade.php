@extends('layouts.admin-layout')
@section('title', 'Chỉnh sửa voucher')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Chỉnh sửa voucher
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <a href="{{ route('admin.voucher-manager') }}" class="cursor-pointer hover:text-[#bc9c75] transition">
            Quản lý mã giảm giá
        </a>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Chỉnh sửa</span>
    </p>
@endsection

@section('content')
    @include('pages.admin.voucher-manager._voucher-form', ['voucher' => $voucher])

@endsection
