@extends('layouts.admin-layout')
@section('title', 'Báo cáo doanh thu')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Báo cáo doanh thu
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Trang chính
        </span>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Doanh thu</span>
    </p>
@endsection

@section('content')
    <div class="space-y-6">
        <form method="GET" action="{{ route('admin.revenue') }}" class="mb-6 flex justify-between items-center">
            <div class="flex gap-2 bg-white p-1 rounded-xl border border-gray-100 shadow-sm">
                <button type="submit" name="period" value="week"
                    class="px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase {{ $period === 'week' ? 'bg-[#fcfaf8] text-[#bc9c75]' : 'text-gray-400' }}">
                    Tuần
                </button>
                <button type="submit" name="period" value="month"
                    class="px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase {{ $period === 'month' ? 'bg-[#fcfaf8] text-[#bc9c75]' : 'text-gray-400' }}">
                    Tháng
                </button>
            </div>
            <a href="{{ route('admin.orders') }}"
                class="text-sm text-gray-500 hover:text-[#bc9c75] font-medium inline-flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i>
                Xem chi tiết đơn hàng
            </a>
        </form>

        @php
            $growthPositive = $summary['growth_percent'] >= 0;
            $maxBarValue = max((float) $monthlyRevenue->max('value'), 1);
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-50 shadow-sm relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-5 text-4xl"><i class="fa-solid fa-coins"></i></div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Doanh thu thuần</p>
                <h3 class="text-2xl font-black text-gray-800 tracking-tighter">
                    {{ number_format((float) $summary['net_revenue'], 0, ',', '.') }}đ
                </h3>
                <p
                    class="text-[10px] {{ $growthPositive ? 'text-green-500' : 'text-rose-500' }} font-bold mt-2 flex items-center gap-1">
                    <i class="fa-solid {{ $growthPositive ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                    {{ $growthPositive ? '+' : '' }}{{ number_format((float) $summary['growth_percent'], 1) }}%
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-50 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tổng giảm giá (Voucher)</p>
                <h3 class="text-2xl font-black text-red-400 tracking-tighter">
                    -{{ number_format((float) $summary['discount_total'], 0, ',', '.') }}đ
                </h3>
                <div class="w-full bg-gray-100 h-1 rounded-full mt-3 overflow-hidden">
                    <div class="bg-red-400 h-full shadow-[0_0_8px_rgba(248,113,113,0.4)]"
                        style="width: {{ $summary['gross_revenue'] > 0 ? min(100, round(($summary['discount_total'] / $summary['gross_revenue']) * 100)) : 0 }}%">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-50 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tổng đơn hợp lệ</p>
                <h3 class="text-2xl font-black text-gray-800 tracking-tighter">
                    {{ number_format((int) $summary['order_count']) }}</h3>
                <p class="text-[10px] text-gray-400 font-medium mt-2 italic">Trong kỳ đã chọn</p>
            </div>

            <div class="bg-[#bc9c75] p-6 rounded-2xl shadow-xl shadow-[#bc9c75]/20 text-white">
                <p class="text-[10px] font-black opacity-80 uppercase tracking-widest mb-1">Giá trị đơn trung bình</p>
                <h3 class="text-2xl font-black tracking-tighter">
                    {{ number_format((float) $summary['avg_order_value'], 0, ',', '.') }}đ
                </h3>
                <p class="text-[10px] font-bold mt-2 bg-white/20 inline-block px-2 py-0.5 rounded italic">
                    Doanh thu gốc: {{ number_format((float) $summary['gross_revenue'], 0, ',', '.') }}đ
                </p>
            </div>
        </div>

        <div class="bg-white p-7 rounded-3xl border border-gray-50 shadow-sm">
            <div class="flex justify-between items-center mb-8">
                <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest">Biểu đồ doanh thu 6 tháng gần nhất
                </h4>
                <i class="fa-solid fa-ellipsis-vertical text-gray-300 cursor-pointer"></i>
            </div>
            <div class="flex items-end justify-between h-48 gap-4 px-4">
                @foreach ($monthlyRevenue as $item)
                    <div class="flex-1 bg-gray-50 rounded-t-lg relative group"
                        style="height: {{ $item['height_percent'] }}%">
                        <div
                            class="absolute -top-6 left-1/2 -translate-x-1/2 text-[9px] font-bold text-gray-400 opacity-0 group-hover:opacity-100 transition-all whitespace-nowrap">
                            {{ number_format((float) $item['value'], 0, ',', '.') }}đ
                        </div>
                        <div
                            class="absolute inset-x-0 bottom-0 bg-[#bc9c75]/30 group-hover:bg-[#bc9c75] transition-all rounded-t-lg h-full shadow-[0_0_15px_rgba(188,156,117,0.3)]">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                @foreach ($monthlyRevenue as $item)
                    <span>{{ $item['label'] }}</span>
                @endforeach
            </div>

            <div class="mt-6 rounded-2xl border border-gray-100 bg-[#fcfaf8] p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Ghi chú</p>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Biểu đồ đang hiển thị doanh thu thuần theo đơn hợp lệ (không bao gồm trạng thái đã hủy/lỗi thanh toán).
                    Mốc cao nhất trong 6 tháng là {{ number_format($maxBarValue, 0, ',', '.') }}đ.
                </p>
            </div>
        </div>
    </div>
@endsection
