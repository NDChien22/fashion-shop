@extends('layouts.user-static-layout')
@section('title', 'Ví voucher')

@section('main-content')
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-800 uppercase tracking-wider">Ví voucher của tôi</h1>
                    <p class="mt-2 text-sm text-gray-500">Danh sách mã giảm giá bạn đã lưu.</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-black text-[#bc9c75] leading-none">{{ $userVouchers->count() }}</div>
                    <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mt-2">Voucher Đã Lưu</p>
                </div>
            </div>
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
                <i class="ri-coupon-line text-4xl text-gray-300 mb-3 block"></i>
                <p class="font-semibold">Bạn chưa lưu voucher nào.</p>
                <p class="text-sm mt-1">Hãy khám phá các voucher hấp dẫn từ shop!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($userVouchers as $userVoucher)
                    @php
                        $voucher = $userVoucher->voucher;
                    @endphp

                    @if ($voucher)
                        <div
                            class="group relative rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full
                                    @if (\App\Enums\VoucherStatus::from($userVoucher->status) === \App\Enums\VoucherStatus::USED) bg-gray-100 text-gray-600
                                    @else
                                        bg-emerald-50 text-emerald-600 @endif
                                ">
                                    <i class="ri-check-circle-line"></i>
                                    {{ \App\Enums\VoucherStatus::from($userVoucher->status)->label() }}
                                </span>
                            </div>

                            <!-- Voucher Code -->
                            <div class="mb-4">
                                <div class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold mb-2">Mã giảm giá
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-xl font-black text-[#bc9c75] font-mono tracking-wider">
                                        {{ $voucher->code }}</div>
                                    <button onclick="copyToClipboard('{{ $voucher->code }}')"
                                        class="text-gray-400 hover:text-[#bc9c75] transition p-2" title="Sao chép">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4 pb-4 border-b border-gray-100">
                                <!-- Discount Info -->
                                <div class="flex justify-between items-start">
                                    <span class="text-xs text-gray-600">Loại giảm:</span>
                                    <span class="font-semibold text-gray-800">
                                        @if ($voucher->discount_type === 'percent')
                                            <span
                                                class="text-lg font-black text-red-500">{{ rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') }}%</span>
                                        @elseif ($voucher->discount_type === 'shipping')
                                            <span class="text-sm">Giảm phí vận chuyển</span>
                                        @else
                                            <span
                                                class="text-lg font-black text-red-500">{{ number_format((float) $voucher->discount_value, 0, ',', '.') }}đ</span>
                                        @endif
                                    </span>
                                </div>

                                <!-- Min Order -->
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-600">Đơn tối thiểu:</span>
                                    <span
                                        class="font-semibold text-gray-800">{{ number_format((float) $voucher->min_order_value, 0, ',', '.') }}đ</span>
                                </div>

                                <!-- Expiry Date -->
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-600">Hết hạn:</span>
                                    <span
                                        class="font-semibold 
                                        @if (\Carbon\Carbon::parse($voucher->end_date)->isPast()) text-gray-400 line-through
                                        @else
                                            text-gray-800 @endif
                                    ">
                                        {{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <button onclick="copyToClipboard('{{ $voucher->code }}')"
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-[#bc9c75] to-[#a68560] text-white text-xs font-bold uppercase tracking-wide rounded-lg hover:shadow-lg transition-all active:scale-95">
                                <i class="ri-file-copy-line mr-1"></i>Sao chép mã
                            </button>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Dispatch event for toast notification
                window.dispatchEvent(new CustomEvent('copy-voucher-code', {
                    detail: {
                        code: text
                    }
                }));
            }).catch(err => {
                console.error('Failed to copy:', err);
            });
        }
    </script>
@endsection
