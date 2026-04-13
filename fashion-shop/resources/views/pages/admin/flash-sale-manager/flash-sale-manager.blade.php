@extends('layouts.admin-layout')
@section('title', 'Chương trình khuyến mãi')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Quản lý flash sale
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Trang chính
        </span>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Quản lý flash sale</span>
    </p>
@endsection

@section('content')
    @livewire('admin.flash-sale-manager')
@endsection
