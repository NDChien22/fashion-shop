@php
    $bestSellerItems = collect($bestSellerProducts ?? []);
@endphp

@if ($bestSellerItems->isNotEmpty())
    <section class="py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-wider">Sản phẩm bán chạy</h2>
                    <div class="h-1 w-20 bg-[#bc9c75] mt-2"></div>
                </div>
                <a wire:navigate href="{{ route('user.product', ['filter' => 'best-seller']) }}"
                    class="text-[#bc9c75] font-medium hover:underline transition-all">
                    Xem tất cả <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-6">
                @foreach ($bestSellerItems as $product)
                    <x-user.product-card :product="$product" :sold-label="'Đã bán ' . number_format((int) ($product->sold_qty ?? 0))" />
                @endforeach
            </div>
        </div>
    </section>
@endif
