@php
    $isHomePage = request()->routeIs('dashboard') || request()->routeIs('user.home');

    $voucherScope = function ($voucher) {
        if (!empty($voucher->category_id) && !empty($voucher->categoryDetail?->name)) {
            return 'Danh mục: ' . $voucher->categoryDetail->name;
        }

        $minimum = (float) ($voucher->min_order_value ?? 0);

        if ($minimum > 0) {
            return 'Đơn từ ' . number_format($minimum, 0, ',', '.') . 'đ';
        }

        return 'Tất cả sản phẩm';
    };

    $voucherDiscount = function ($voucher) {
        if ($voucher->discount_type === 'percent') {
            return 'GIẢM ' . rtrim(rtrim(number_format((float) $voucher->discount_value, 2, '.', ''), '0'), '.') . '%';
        }

        if ($voucher->discount_type === 'shipping') {
            return 'FREESHIP';
        }

        return 'GIẢM ' . number_format((float) $voucher->discount_value, 0, ',', '.') . 'đ';
    };
@endphp

<div>
    @if ($vouchers->isNotEmpty())
        <section class="{{ $isHomePage ? 'max-w-7xl mx-auto px-4 py-8' : 'px-0 sm:px-2 mb-2' }}">
            <div class="flex items-end justify-between mb-4 md:mb-5">
                <div>
                    <h3 class="text-sm md:text-base font-black uppercase tracking-[0.18em] text-gray-900">Ưu đãi voucher
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">Lưu nhanh voucher để áp dụng khi thanh toán.</p>
                </div>

                @if ($isHomePage)
                    <a href="{{ route('user.vouchers') }}"
                        class="text-xs md:text-sm font-semibold text-[#bc9c75] hover:text-[#9d7a4a] transition-colors">
                        Xem ví voucher
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($vouchers as $voucher)
                    @php
                        $isSaved = auth()->check() && in_array((int) $voucher->id, $savedVoucherIds, true);
                    @endphp

                    <article
                        class="relative overflow-hidden rounded-2xl border border-[#f1e1d0] bg-linear-to-br from-white to-[#fffaf4] p-4 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <div class="absolute -right-9 -top-9 h-24 w-24 rounded-full bg-[#ffe5cc]/60 blur-xl"></div>

                        <div class="relative">
                            <div class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#5f89c9] truncate">
                                {{ $voucherScope($voucher) }}
                            </div>

                            <h4 class="mt-1 text-red-500 font-black text-base md:text-lg uppercase leading-tight">
                                {{ $voucherDiscount($voucher) }}
                            </h4>

                            <div class="mt-2 flex items-center justify-between gap-3">
                                <span class="text-[11px] md:text-xs text-gray-400">HSD:
                                    {{ \Illuminate\Support\Carbon::parse($voucher->end_date)->format('d/m/Y') }}</span>
                                <span
                                    class="inline-flex items-center rounded-full bg-white border border-[#f2e4d4] px-2.5 py-1 text-[10px] font-bold tracking-wide text-gray-600">
                                    {{ $voucher->code }}
                                </span>
                            </div>

                            <div class="mt-4">
                                @auth
                                    @if ($isSaved)
                                        <button type="button"
                                            class="w-full h-9 rounded-xl bg-emerald-500/90 text-white text-[11px] font-bold uppercase tracking-wide cursor-default">
                                            Đã lưu
                                        </button>
                                    @else
                                        <button type="button" wire:click="saveVoucher({{ $voucher->id }})"
                                            wire:loading.attr="disabled" wire:target="saveVoucher({{ $voucher->id }})"
                                            class="w-full h-9 rounded-xl bg-[#ff4d4f] text-white text-[11px] font-bold uppercase tracking-wide transition-all duration-200 hover:brightness-105 active:scale-[0.98] disabled:opacity-70">
                                            <span wire:loading.remove wire:target="saveVoucher({{ $voucher->id }})">Lấy
                                                mã</span>
                                            <span wire:loading wire:target="saveVoucher({{ $voucher->id }})">Đang
                                                lưu...</span>
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                        class="inline-flex w-full items-center justify-center h-9 rounded-xl bg-[#ff4d4f] text-white text-[11px] font-bold uppercase tracking-wide transition-all duration-200 hover:brightness-105 active:scale-[0.98]">
                                        Lấy mã
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
