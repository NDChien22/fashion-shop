@extends('layouts.user-static-layout')
@section('title', 'Ví voucher')

@section('main-content')
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-800 uppercase tracking-wider">Ví voucher của tôi</h1>
            <p class="mt-2 text-sm text-gray-500">Danh sách mã giảm giá bạn đã lưu.</p>
        </div>

        @if (session('success'))
            <div
                class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl bg-red-50 border border-red-100 text-red-600 px-4 py-3 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @if ($userVouchers->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-12 text-center text-gray-500">
                Bạn chưa lưu voucher nào.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($userVouchers as $userVoucher)
                    @php
                        $voucher = $userVoucher->voucher;
                    @endphp

                    @if ($voucher)
                        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                            <div class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold">Mã voucher</div>
                            <div class="mt-1 text-lg font-black text-[#bc9c75]">{{ $voucher->code }}</div>

                            <div class="mt-3 text-sm text-gray-700">
                                Loại giảm:
                                <span class="font-semibold">
                                    @if ($voucher->discount_type === 'percent')
                                        {{ rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') }}%
                                    @elseif ($voucher->discount_type === 'shipping')
                                        Giảm phí vận chuyển
                                    @else
                                        {{ number_format((float) $voucher->discount_value, 0, ',', '.') }}đ
                                    @endif
                                </span>
                            </div>

                            <div class="mt-1 text-sm text-gray-700">
                                Đơn tối thiểu: <span
                                    class="font-semibold">{{ number_format((float) $voucher->min_order_value, 0, ',', '.') }}đ</span>
                            </div>

                            <div class="mt-1 text-sm text-gray-700">
                                Trạng thái:
                                <span
                                    class="font-semibold {{ $userVoucher->status === 'used' ? 'text-gray-500' : 'text-emerald-600' }}">
                                    {{ $userVoucher->status === 'used' ? 'Đã sử dụng' : 'Chưa sử dụng' }}
                                </span>
                            </div>

                            <div
                                class="mt-3 inline-flex items-center rounded-full bg-gray-50 px-3 py-1 text-[11px] font-bold text-gray-500">
                                HSD: {{ \Illuminate\Support\Carbon::parse($voucher->end_date)->format('d/m/Y') }}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endsection
