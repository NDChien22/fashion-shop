@extends('layouts.admin-layout')
@section('title', 'Quản lý danh mục sản phẩm')

@section('page-header')
    <h1 class="text-xl font-semibold text-gray-800">Quản lý danh mục sản phẩm</h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Trang chính</span> /
        <span class="text-[#bc9c75] font-medium">Danh mục sản phẩm</span>
    </p>
@endsection

@section('content')
    <div class="space-y-6">
        @if ($errors->has('category_delete'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('category_delete') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <form action="{{ route('admin.add-product-category-handler') }}" method="POST"
                class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4 shadow-sm">
                @csrf
                <input type="hidden" name="type" value="parent">

                <h3 class="text-sm font-bold text-gray-800">Thêm danh mục cha</h3>

                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Tên danh mục cha</label>
                    <input type="text" name="name" value="{{ old('type') === 'parent' ? old('name') : '' }}"
                        placeholder="Ví dụ: Thời trang nam"
                        class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs border border-gray-100">
                    @if ($errors->any() && old('type') === 'parent')
                        <p class="text-xs text-red-500 mt-1">{{ $errors->first('name') }}</p>
                    @endif
                </div>

                <button type="submit"
                    class="px-5 py-2 bg-[#bc9c75] text-white rounded-xl text-xs font-semibold hover:opacity-90">
                    Thêm danh mục cha
                </button>
            </form>

            <form action="{{ route('admin.add-product-category-handler') }}" method="POST"
                class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4 shadow-sm">
                @csrf
                <input type="hidden" name="type" value="child">

                <h3 class="text-sm font-bold text-gray-800">Thêm danh mục con</h3>

                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Danh mục cha</label>
                    <select name="parent_id"
                        class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs border border-gray-100">
                        <option value="">-- Chọn danh mục cha --</option>
                        @foreach ($parentCategories as $parentCategory)
                            <option value="{{ $parentCategory->id }}"
                                {{ old('parent_id') == $parentCategory->id ? 'selected' : '' }}>
                                {{ $parentCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->any() && old('type') === 'child')
                        <p class="text-xs text-red-500 mt-1">{{ $errors->first('parent_id') }}</p>
                    @endif
                </div>

                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase">Tên danh mục con</label>
                    <input type="text" name="name" value="{{ old('type') === 'child' ? old('name') : '' }}"
                        placeholder="Ví dụ: Áo polo"
                        class="w-full mt-1 bg-gray-50 rounded-xl py-2.5 px-4 text-xs border border-gray-100">
                    @if ($errors->any() && old('type') === 'child')
                        <p class="text-xs text-red-500 mt-1">{{ $errors->first('name') }}</p>
                    @endif
                </div>

                <button type="submit"
                    class="px-5 py-2 bg-[#bc9c75] text-white rounded-xl text-xs font-semibold hover:opacity-90">
                    Thêm danh mục con
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800">Danh sách danh mục</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-[#fcfaf8] border-b border-gray-100 text-gray-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="text-left px-6 py-3">Tên danh mục</th>
                            <th class="text-left px-6 py-3">Danh mục cha</th>
                            <th class="text-center px-6 py-3">Sản phẩm</th>
                            <th class="text-right px-6 py-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($parentCategories as $parentCategory)
                            @php
                                $parentTotalProducts =
                                    (int) $parentCategory->products_count +
                                    (int) $parentCategory->children->sum('products_count');
                            @endphp
                            <tr>
                                <td class="px-6 py-3 font-semibold text-gray-800">{{ $parentCategory->name }}</td>
                                <td class="px-6 py-3 text-gray-400">-</td>
                                <td class="px-6 py-3 text-center">{{ $parentTotalProducts }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.product-manager', ['category' => $parentCategory->id]) }}"
                                        class="text-[#bc9c75] hover:underline mr-3">
                                        Quản lý SP
                                    </a>
                                    <form action="{{ route('admin.product-categories.destroy', $parentCategory->id) }}"
                                        method="POST" class="inline" data-confirm-delete="1"
                                        data-confirm-message="Bạn có chắc muốn xóa danh mục này?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-600">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            @foreach ($parentCategory->children as $child)
                                <tr>
                                    <td class="px-6 py-3 text-gray-700">- {{ $child->name }}</td>
                                    <td class="px-6 py-3">{{ $parentCategory->name }}</td>
                                    <td class="px-6 py-3 text-center">{{ (int) $child->products_count }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('admin.product-manager', ['category' => $child->id]) }}"
                                            class="text-[#bc9c75] hover:underline mr-3">
                                            Quản lý SP
                                        </a>
                                        <form action="{{ route('admin.product-categories.destroy', $child->id) }}"
                                            method="POST" class="inline" data-confirm-delete="1"
                                            data-confirm-message="Bạn có chắc muốn xóa danh mục này?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-600">
                                                Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">Chưa có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
