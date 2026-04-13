@php
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

<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-3">
        <h3 class="text-sm md:text-base font-black uppercase tracking-[0.2em] text-gray-800">Ưu đãi voucher</h3>
        <p class="mt-1 text-xs text-gray-500">Lưu voucher ngay nếu bạn đã đăng nhập.</p>
    </div>

    @if ($vouchers->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            @foreach ($vouchers as $voucher)
                @php
                    $isSaved = auth()->check() && in_array((int) $voucher->id, $savedVoucherIds, true);
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
                                <button type="button" wire:click="saveVoucher({{ $voucher->id }})"
                                    wire:loading.attr="disabled"
                                    class="w-full py-2 rounded-xl bg-[#bc9c75] text-white text-sm font-bold hover:brightness-110 transition disabled:opacity-70">
                                    <span wire:loading.remove wire:target="saveVoucher({{ $voucher->id }})">Lưu vào tài
                                        khoản</span>
                                    <span wire:loading wire:target="saveVoucher({{ $voucher->id }})">Đang lưu...</span>
                                </button>
                            @endif
                        @else
                            <button type="button" wire:click="copyVoucherCode('{{ $voucher->code }}')"
                                class="w-full py-2 rounded-xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition">
                                Sao chép mã voucher
                            </button>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-10 text-center">
            <p class="text-sm font-semibold text-gray-600">Không tìm thấy voucher phù hợp.</p>
            <p class="mt-1 text-xs text-gray-400">Thử đổi từ khóa tìm kiếm khác.</p>
        </div>
    @endif
</div>
