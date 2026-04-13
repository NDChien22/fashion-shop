@extends('layouts.admin-layout')
@section('title', 'Banner')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Quản lý banner
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Trang chính</span>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Quản lý banner</span>
    </p>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="rounded-[28px] bg-white p-5 shadow-xl shadow-gray-100 border border-gray-50">
                <div class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Tổng banner</div>
                <div class="mt-3 text-3xl font-black text-gray-800">{{ $banners->count() }}</div>
            </div>
            <div class="rounded-[28px] bg-white p-5 shadow-xl shadow-gray-100 border border-gray-50">
                <div class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Đang kích hoạt</div>
                <div class="mt-3 text-3xl font-black text-emerald-600">{{ $banners->where('is_active', true)->count() }}
                </div>
            </div>
            <div class="rounded-[28px] bg-white p-5 shadow-xl shadow-gray-100 border border-gray-50">
                <div class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Đã lên lịch</div>
                <div class="mt-3 text-3xl font-black text-sky-600">{{ $banners->whereNotNull('start_date')->count() }}</div>
            </div>
        </div>

        <div class="bg-white rounded-4xl shadow-2xl shadow-gray-200/40 border border-gray-50 overflow-hidden">
            <div class="px-6 md:px-8 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Danh sách banner</h3>
                    <p class="text-sm text-gray-500 mt-1">Theo dõi trạng thái và ngày áp dụng của từng banner.</p>
                </div>
                <a href="{{ route('admin.add-banner') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-xs uppercase tracking-[0.2em] hover:bg-gray-200 transition">
                    <i class="fa-solid fa-plus"></i>
                    Tạo mới
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-275">
                    <thead class="bg-gray-50 text-[11px] uppercase tracking-[0.2em] text-gray-400">
                        <tr>
                            <th class="text-left px-6 py-4 font-bold">Banner</th>
                            <th class="text-left px-6 py-4 font-bold">Tiêu đề</th>
                            <th class="text-left px-6 py-4 font-bold">Thời gian</th>
                            <th class="text-left px-6 py-4 font-bold">Trạng thái</th>
                            <th class="text-right px-6 py-4 font-bold">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($banners as $banner)
                            @php
                                $isExternalImage = \Illuminate\Support\Str::startsWith($banner->image_url, [
                                    'http://',
                                    'https://',
                                    '/',
                                ]);
                                $bannerImage = $isExternalImage
                                    ? $banner->image_url
                                    : \Illuminate\Support\Facades\Storage::url($banner->image_url);
                                $isScheduled = $banner->start_date || $banner->end_date;
                                $isOngoing = !$banner->start_date || $banner->start_date->isPast();
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-6 py-5">
                                    <div class="w-28 h-16 rounded-2xl overflow-hidden border border-gray-200 bg-gray-100">
                                        <img src="{{ $bannerImage }}" alt="{{ $banner->title }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="px-6 py-5 align-top">
                                    <div class="font-bold text-gray-800">{{ $banner->title }}</div>
                                    <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                                        <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 font-semibold">
                                            {{ $banner->targetLabel() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 align-top text-sm text-gray-600">
                                    <div class="space-y-1">
                                        <div>Bắt đầu: <span
                                                class="font-semibold text-gray-800">{{ $banner->start_date ? $banner->start_date->format('d/m/Y') : 'Không giới hạn' }}</span>
                                        </div>
                                        <div>Kết thúc: <span
                                                class="font-semibold text-gray-800">{{ $banner->end_date ? $banner->end_date->format('d/m/Y') : 'Không giới hạn' }}</span>
                                        </div>
                                    </div>
                                    @if ($isScheduled)
                                        <div class="mt-2 text-[11px] text-gray-400 uppercase tracking-[0.2em]">
                                            {{ $isOngoing ? 'Đang hoạt động' : 'Chưa bắt đầu' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-5 align-top">
                                    @if ($banner->is_active)
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            Đang hiển thị
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 text-gray-500 text-xs font-bold">
                                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                            Tạm ẩn
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 align-top text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.edit-banner', $banner) }}"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 text-gray-600 hover:bg-[#bc9c75] hover:text-white transition">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.delete-banner', $banner) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="mx-auto max-w-md">
                                        <div
                                            class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center text-gray-300 text-2xl">
                                            <i class="fa-regular fa-images"></i>
                                        </div>
                                        <h4 class="mt-4 text-lg font-bold text-gray-800">Chưa có banner nào</h4>
                                        <p class="mt-2 text-sm text-gray-500">Tạo banner đầu tiên để hiển thị lên trang chủ
                                            và phục vụ chiến dịch quảng bá.</p>
                                        <a href="{{ route('admin.add-banner') }}"
                                            class="mt-5 inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-[#bc9c75] text-white font-bold text-xs uppercase tracking-[0.2em] shadow-lg shadow-[#bc9c75]/20">
                                            <i class="fa-solid fa-circle-plus"></i>
                                            Thêm banner
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
