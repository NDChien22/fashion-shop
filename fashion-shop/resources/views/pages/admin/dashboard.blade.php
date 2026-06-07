@extends('layouts.admin-layout')
@section('title', 'Admin Dashboard')

@section('page-header')

    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Tổng quan
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Trang chính
        </span> /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Tổng quan</span>
    </p>

@endsection

@section('content')

    @php
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'returned' => 'Đã trả hàng',
            'exchanged' => 'Đã đổi hàng',
            'cancelled' => 'Đã hủy',
            'payment_failed' => 'Lỗi thanh toán',
            'shipping' => 'Đang giao',
            'delivered' => 'Đã giao',
        ];
    @endphp

    <div class="space-y-6">
        <div class="bg-[#e6c9ad] rounded-2xl p-6 flex justify-between items-center">
            <div class="max-w-xl">
                <h2 class="text-xl font-semibold text-[#4a3a2a] mb-2">
                    Chào mừng trở lại
                </h2>

                <p class="text-[#5d4a37] text-sm">
                    Hôm nay có <b>{{ number_format($stats['new_orders_today']) }} đơn hàng mới</b> và
                    <b>{{ number_format((float) $stats['revenue_today'], 0, ',', '.') }}đ doanh thu</b>.
                </p>

                <a href="{{ route('admin.orders') }}"
                    class="mt-3 inline-flex bg-[#bc9c75] text-white px-4 py-2 rounded-lg text-sm hover:opacity-90">
                    Xem đơn hàng
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-400">Doanh thu hôm nay</p>
                <h3 class="text-xl font-semibold mt-1">
                    {{ number_format((float) $stats['revenue_today'], 0, ',', '.') }}đ
                </h3>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-400">Sản phẩm bán hôm nay</p>
                <h3 class="text-xl font-semibold mt-1">{{ number_format($stats['products_sold_today']) }}</h3>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-400">Khách hàng mới hôm nay</p>
                <h3 class="text-xl font-semibold mt-1">{{ number_format($stats['new_customers_today']) }}</h3>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-400">Đơn chờ xử lý</p>
                <h3 class="text-xl font-semibold mt-1">{{ number_format($stats['pending_orders']) }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-700">Đổi/trả</h3>
                    <p class="text-xs text-gray-400 mt-1">Chỉ hiển thị số đơn cần xử lý</p>
                </div>

                <a href="{{ route('admin.return-requests') }}" class="text-xs font-semibold text-[#bc9c75] hover:underline">
                    Xem chi tiết
                </a>
            </div>

            <div class="p-5">
                <div class="rounded-xl bg-amber-50 p-5 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">Cần xử lý</p>
                        <h3 class="mt-1 text-3xl font-black text-amber-700">
                            {{ number_format($stats['return_requests_pending']) }}
                        </h3>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-amber-600">Đơn đổi/trả đang chờ duyệt</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-black uppercase tracking-widest text-gray-700">Đơn hàng gần đây</h3>
                <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-[#bc9c75] hover:underline">
                    Xem tất cả
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#fcfaf8] border-b border-gray-100 text-gray-500">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider font-bold">Mã đơn</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider font-bold">Khách hàng</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider font-bold">Sản phẩm</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider font-bold">Tổng tiền</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider font-bold">Trạng thái</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider font-bold">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td class="px-5 py-3 font-semibold text-gray-800">{{ $order->order_code }}</td>
                                <td class="px-5 py-3 text-gray-700">
                                    {{ $order->user?->full_name ?? ($order->guest_name ?? 'Khách vãng lai') }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ number_format($order->items->sum('quantity')) }} SP
                                </td>
                                <td class="px-5 py-3 font-semibold text-[#bc9c75]">
                                    {{ number_format((float) $order->final_amount, 0, ',', '.') }}đ
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    Chưa có dữ liệu đơn hàng.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
