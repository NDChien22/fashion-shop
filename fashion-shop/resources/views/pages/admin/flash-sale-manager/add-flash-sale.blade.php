@extends('layouts.admin-layout')
@section('title', 'Thêm flash sale')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Thêm flash sale
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <a href="{{ route('admin.flash-sale-manager') }}" class="cursor-pointer hover:text-[#bc9c75] transition">
            Quản lý flash sale
        </a>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Thêm mới</span>
    </p>
@endsection

@section('content')
    @include('pages.admin.flash-sale-manager._flash-sale-form')
@endsection
