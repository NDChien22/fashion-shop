@extends('layouts.admin-layout')
@section('title', 'Quản lý đổi / trả')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Quản lý đổi / trả
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Trang chính
        </span>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Đổi / trả</span>
    </p>
@endsection

@section('content')
    <livewire:admin.return-request-manager />
@endsection
