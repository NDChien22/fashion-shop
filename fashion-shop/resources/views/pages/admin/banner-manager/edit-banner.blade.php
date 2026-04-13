@extends('layouts.admin-layout')
@section('title', 'Cập nhật banner')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">Cập nhật banner</h1>

    <p class="text-xs text-gray-400 mt-1">
        <a href="{{ route('admin.banner-manager') }}" class="cursor-pointer hover:text-[#bc9c75] transition">Quản lý
            banner</a>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Cập nhật banner</span>
    </p>
@endsection

@section('content')
    @include('pages.admin.banner-manager._banner-form', ['banner' => $banner])
@endsection
