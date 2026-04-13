@php
    $vouchers = collect($activeVouchers ?? []);
    $savedVoucherIds = collect($collectedVoucherIds ?? [])
        ->map(fn($id) => (int) $id)
        ->values();

    $formatDiscount = function ($voucher) {
        if ($voucher->discount_type === 'percent') {
            return 'Giam ' . rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') . '%';
        }

        if ($voucher->discount_type === 'shipping') {
            return 'Giam phi van chuyen';
        }

        return 'Giam ' . number_format((float) $voucher->discount_value, 0, ',', '.') . 'd';
    };
@endphp

@if ($vouchers->isNotEmpty())
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm md:text-base font-black uppercase tracking-[0.2em] text-gray-800">Ưu đãi voucher</h3>
            <a href="{{ route('user.product') }}" class="text-xs font-semibold text-[#bc9c75] hover:underline">
                Xem sản phẩm
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            @foreach ($vouchers as $voucher)
                @php
                    $isSaved = $savedVoucherIds->contains((int) $voucher->id);
                @endphp
                <div class="relative overflow-hidden rounded-2xl border border-red-100 bg-white p-4 shadow-sm">
                    <div class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold">Mã voucher</div>
                    <div class="mt-1 text-lg font-black text-red-500">{{ $voucher->code }}</div>

                    <div class="mt-2 text-sm font-semibold text-gray-800">{{ $formatDiscount($voucher) }}</div>
                    <div class="mt-1 text-xs text-gray-500">
                        Đơn tối thiểu {{ number_format((float) $voucher->min_order_value, 0, ',', '.') }}d
                    </div>

                    @if (!is_null($voucher->max_discount) && $voucher->discount_type === 'percent')
                        <div class="mt-1 text-xs text-gray-500">
                            Giảm tối đa {{ number_format((float) $voucher->max_discount, 0, ',', '.') }}d
                        </div>
                    @endif

                    <div
                        class="mt-3 inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-[11px] font-bold text-red-500">
                        HSD: {{ \Illuminate\Support\Carbon::parse($voucher->end_date)->format('d/m/Y') }}
                    </div>

                    <div class="mt-4">
                        @auth
                            @if ($isSaved)
                                <button type="button"
                                    class="w-full py-2 rounded-xl bg-emerald-50 text-emerald-600 text-sm font-bold cursor-default">
                                    Đã lưu vào ví voucher
                                </button>
                            @else
                                <form action="{{ route('user.vouchers.collect', $voucher) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full py-2 rounded-xl bg-[#bc9c75] text-white text-sm font-bold hover:brightness-110 transition">
                                        Lưu vào tài khoản
                                    </button>
                                </form>
                            @endif
                        @else
                            <form action="{{ route('user.vouchers.copy', $voucher) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2 rounded-xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition">
                                    Lấy mã voucher
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
