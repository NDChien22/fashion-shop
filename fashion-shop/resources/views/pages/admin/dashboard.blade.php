@extends('layouts.admin-layout')
@section('title', 'Admin Dashboard')
@section('content')

    <div>
        <div class="tab-content active">
            <div class="space-y-6">
                <div class="bg-[#e6c9ad] rounded-2xl p-6 flex justify-between items-center">
                    <div class="max-w-xl">
                        <h2 class="text-xl font-semibold text-[#4a3a2a] mb-2">
                            Chào mừng trở lại
                        </h2>

                        <p class="text-[#5d4a37] text-sm">
                            Hôm nay cửa hàng có <b>12 đơn hàng mới</b>.
                            Đừng quên kiểm tra kho cho bộ sưu tập Mùa Hè nhé!
                        </p>

                        <button onclick="loadPage('orders')"
                            class="mt-3 bg-[#bc9c75] text-white px-4 py-2 rounded-lg text-sm hover:opacity-90">
                            Xem đơn hàng
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-sm text-gray-400">Doanh thu ngày</p>
                        <h3 class="text-xl font-semibold mt-1">5.200.000đ</h3>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-sm text-gray-400">Sản phẩm bán ra</p>
                        <h3 class="text-xl font-semibold mt-1">42</h3>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-sm text-gray-400">Khách hàng mới</p>
                        <h3 class="text-xl font-semibold mt-1">15</h3>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-sm text-gray-400">Đánh giá tốt</p>
                        <h3 class="text-xl font-semibold mt-1">98%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
