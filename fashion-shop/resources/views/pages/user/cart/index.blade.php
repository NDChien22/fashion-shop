@extends('layouts.user-static-layout')
@section('title', 'Giỏ hàng')

@section('main-content')
    <div class="container mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold uppercase mb-8 border-b-2 border-[#bc9c75] inline-block pb-2">Giỏ hàng của bạn</h2>

        <div class="flex flex-col lg:flex-row gap-8">
            <div id="cart-content-page" class="flex-1 space-y-4"></div>

            <div class="w-full lg:w-1/3">
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 sticky top-24">
                    <h3 class="text-lg font-bold mb-4">Tóm tắt đơn hàng</h3>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Tạm tính:</span>
                        <span id="page-cart-subtotal" class="font-medium">0₫</span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Giao hàng:</span>
                        <span class="text-green-600">Miễn phí</span>
                    </div>
                    <div class="border-t pt-4 flex justify-between mb-6">
                        <span class="text-xl font-bold">Tổng tiền:</span>
                        <span id="page-cart-total" class="text-xl font-bold text-[#bc9c75]">0₫</span>
                    </div>
                    <button
                        class="w-full bg-[#c5a059] text-white py-4 rounded-lg font-bold uppercase hover:bg-[#c8b593] transition-colors">
                        Thanh toán ngay
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
