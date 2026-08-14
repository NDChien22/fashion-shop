@php
    $activeSales = collect($activeFlashSales ?? []);
    $flashItems = collect($flashSaleProducts ?? []);
@endphp

@if ($activeSales->isNotEmpty() && $flashItems->isNotEmpty())
    <section class="py-14 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-gold italic uppercase tracking-tighter">Flash Sale</h2>
                    <div class="h-1 w-20 bg-gold mt-2"></div>
                </div>
                <a wire:navigate href="{{ route('user.product', ['filter' => 'flash-sale']) }}"
                    class="text-sm font-bold text-gray-400 hover:text-gold transition underline uppercase tracking-widest">
                    Xem tất cả <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-6">
                @foreach ($flashItems as $product)
                    <x-user.product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
@endif
