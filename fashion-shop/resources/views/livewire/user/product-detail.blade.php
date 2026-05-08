<div class="min-h-screen bg-linear-to-b from-[#f7f2eb] via-[#fffdfa] to-[#f5f1ea] py-5 md:py-8">
    <div class="max-w-5xl mx-auto px-3.5 sm:px-4.5 lg:px-5">
        <nav class="mb-4 flex flex-wrap items-center gap-1.5 text-xs text-stone-500">
            <a wire:navigate href="{{ route('dashboard') }}" class="hover:text-[#bc9c75] transition">Trang chủ</a>
            <span>/</span>
            <a wire:navigate href="{{ route('user.product') }}" class="hover:text-[#bc9c75] transition">Sản phẩm</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">{{ $product->name }}</span>
        </nav>

        <section class="grid grid-cols-1 xl:grid-cols-[0.9fr_1.1fr] gap-4 xl:gap-5 items-start">
            <div class="space-y-1.5" x-data="{
                images: @js(array_values($images)),
                currentImage: @js($activeImage),
                currentIndex: 0,
                thumbIndex: 0,
                thumbPerView: 3,
                timer: null,
                resizeHandler: null,
                init() {
                    const foundIndex = this.images.indexOf(this.currentImage);
                    this.currentIndex = foundIndex >= 0 ? foundIndex : 0;
                    this.currentImage = this.images[this.currentIndex] ?? this.currentImage;
                    this.updateThumbPerView();
                    this.ensureCurrentThumbVisible();
                    this.resizeHandler = () => this.updateThumbPerView();
                    window.addEventListener('resize', this.resizeHandler);
                    this.startAutoplay();
                },
                destroy() {
                    this.stopAutoplay();
                    if (this.resizeHandler) {
                        window.removeEventListener('resize', this.resizeHandler);
                    }
                },
                setCurrent(index) {
                    this.currentIndex = index;
                    this.currentImage = this.images[index];
                    this.ensureCurrentThumbVisible();
                },
                nextImage() {
                    if (this.images.length < 2) {
                        return;
                    }
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                    this.currentImage = this.images[this.currentIndex];
                    this.ensureCurrentThumbVisible();
                },
                updateThumbPerView() {
                    const width = window.innerWidth;
                    this.thumbPerView = width >= 1280 ? 5 : (width >= 768 ? 4 : 3);
                    this.clampThumbIndex();
                    this.ensureCurrentThumbVisible();
                },
                maxThumbIndex() {
                    return Math.max(this.images.length - this.thumbPerView, 0);
                },
                clampThumbIndex() {
                    if (this.thumbIndex > this.maxThumbIndex()) {
                        this.thumbIndex = this.maxThumbIndex();
                    }
                },
                thumbNext() {
                    this.thumbIndex = this.thumbIndex >= this.maxThumbIndex() ? 0 : this.thumbIndex + 1;
                },
                thumbPrev() {
                    this.thumbIndex = this.thumbIndex <= 0 ? this.maxThumbIndex() : this.thumbIndex - 1;
                },
                ensureCurrentThumbVisible() {
                    if (this.currentIndex < this.thumbIndex) {
                        this.thumbIndex = this.currentIndex;
                    }
                    const visibleEnd = this.thumbIndex + this.thumbPerView - 1;
                    if (this.currentIndex > visibleEnd) {
                        this.thumbIndex = this.currentIndex - this.thumbPerView + 1;
                    }
                    this.clampThumbIndex();
                },
                startAutoplay() {
                    this.stopAutoplay();
                    if (this.images.length < 2) {
                        return;
                    }
                    this.timer = setInterval(() => this.nextImage(), 2400);
                },
                stopAutoplay() {
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                }
            }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
                <div
                    class="overflow-hidden rounded-4xl bg-white shadow-[0_18px_44px_rgba(15,23,42,0.08)] border border-stone-100 ring-1 ring-stone-100/70">
                    <div
                        class="relative aspect-3/4 xl:min-h-136 bg-linear-to-br from-[#f4efe7] via-[#f8f5ef] to-[#ede6dc]">
                        <img :src="currentImage" alt="{{ $product->name }}"
                            class="absolute inset-0 h-full w-full min-w-full object-cover object-top transition-opacity duration-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-end gap-1" x-show="images.length > thumbPerView">
                        <button type="button" @click="thumbPrev()"
                            class="h-6 w-6 inline-flex items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 hover:border-[#b69066] hover:text-[#7a5d3d] transition"
                            aria-label="Thumbnail trước">
                            <i class="ri-arrow-left-s-line"></i>
                        </button>
                        <button type="button" @click="thumbNext()"
                            class="h-6 w-6 inline-flex items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 hover:border-[#b69066] hover:text-[#7a5d3d] transition"
                            aria-label="Thumbnail tiếp theo">
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>

                    <div class="overflow-hidden pb-0.5">
                        <div class="-mx-1 flex transition-transform duration-500 ease-out"
                            :style="`transform: translateX(-${thumbIndex * (100 / thumbPerView)}%);`">
                            @foreach ($images as $image)
                                <div class="shrink-0 px-1" :style="`width: ${100 / thumbPerView}%`">
                                    <button type="button" @click="setCurrent({{ $loop->index }})"
                                        :class="currentIndex === {{ $loop->index }} ?
                                            'border-[#b69066] ring-2 ring-[#b69066]/20' :
                                            'border-stone-100 hover:border-stone-200'"
                                        class="w-full h-16 md:h-20 overflow-hidden rounded-xl bg-white border transition shadow-sm hover:-translate-y-0.5 hover:shadow-md">
                                        <img src="{{ $image }}" alt="{{ $product->name }}"
                                            class="h-full w-full object-cover">
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:sticky xl:top-3">
                <div
                    class="rounded-4xl bg-white/95 backdrop-blur shadow-[0_18px_52px_rgba(15,23,42,0.10)] border border-stone-100 ring-1 ring-white p-4.5 md:p-5 space-y-4">
                    <div
                        class="flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.18em]">
                        <span
                            class="rounded-full bg-[#b69066]/12 text-[#7a5d3d] px-2.5 py-1">{{ $product->product_code }}</span>
                        <span class="rounded-full bg-stone-100 text-stone-600 px-2.5 py-1">
                            {{ $product->is_active ? 'Đang bán' : 'Tạm ngưng' }}
                        </span>
                        @if ($product->category)
                            <span
                                class="rounded-full bg-stone-100 text-stone-600 px-2.5 py-1">{{ $product->category->name }}</span>
                        @endif
                        @if ($product->collection)
                            <span
                                class="rounded-full bg-stone-100 text-stone-600 px-2.5 py-1">{{ $product->collection->name }}</span>
                        @endif
                    </div>

                    <div class="space-y-2 pb-1 border-b border-stone-100/80">
                        <h1 class="text-xl md:text-[1.65rem] font-black tracking-tight text-gray-900 leading-tight">
                            {{ $product->name }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
                            <div class="flex items-center gap-1 text-amber-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= round($averageRating) ? 'ri-star-fill' : 'ri-star-line' }}"></i>
                                @endfor
                            </div>
                            <span class="font-semibold text-gray-800">{{ number_format($averageRating, 1) }}/5</span>
                            <span>({{ number_format($reviewCount) }} đánh giá)</span>
                            <span>•</span>
                            <span>{{ number_format($totalStock) }} sản phẩm trong kho</span>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-linear-to-br from-[#fbf6ef] to-[#f6eee4] border border-[#ebddcf] p-3.5">
                        <div class="flex items-end justify-between gap-4 flex-wrap">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-[#7a5d3d] font-bold">Giá sản phẩm</p>
                                @php
                                    $isOnSale =
                                        $hasSalePrice &&
                                        $salePrice !== null &&
                                        $salePrice > 0 &&
                                        $salePrice < $basePrice;
                                @endphp

                                <div class="mt-1 flex flex-wrap items-end gap-3">
                                    <p
                                        class="text-2xl md:text-3xl font-black {{ $isOnSale ? 'text-[#c7362f]' : 'text-[#b69066]' }}">
                                        {{ number_format($isOnSale ? $salePrice : $basePrice, 0, ',', '.') }}₫
                                    </p>
                                    @if ($isOnSale)
                                        <p class="text-sm md:text-base font-semibold text-gray-400 line-through">
                                            {{ number_format($basePrice, 0, ',', '.') }}₫
                                        </p>
                                        <span
                                            class="inline-flex items-center rounded-full bg-[#ff4d4f]/10 px-2.5 py-1 text-[11px] font-bold text-[#c7362f]">
                                            -{{ $saleDiscountPercent }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right text-xs text-gray-600">
                                <p class="font-semibold text-gray-900">Biến thể đang chọn</p>
                                <p>{{ $selectedSku?->sku ?? 'Chưa có biến thể' }}</p>
                                <p>
                                    {{ $selectedSku ? 'Tồn kho: ' . number_format((int) $selectedSku->stock) : 'Không có biến thể khả dụng' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 pt-0.5">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-gray-500">Chọn màu</h2>
                                <span
                                    class="text-xs font-semibold text-[#7a5d3d] bg-[#b69066]/12 px-3 py-1 rounded-full">
                                    {{ $selectedColor !== '' ? $selectedColor : 'Chưa chọn' }}
                                </span>
                            </div>

                            @if (!empty($colorOptions))
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($colorOptions as $color)
                                        @php
                                            $isColorAvailable = $variants->contains(function ($variant) use (
                                                $color,
                                                $selectedSize,
                                            ) {
                                                if ((int) ($variant->stock ?? 0) < 1) {
                                                    return false;
                                                }

                                                if (trim((string) ($variant->color ?? '')) !== $color) {
                                                    return false;
                                                }

                                                if ($selectedSize === '') {
                                                    return true;
                                                }

                                                return strtoupper(trim((string) ($variant->size ?? ''))) ===
                                                    $selectedSize;
                                            });
                                        @endphp
                                        <button type="button" wire:click="selectColor(@js($color))"
                                            @disabled(!$isColorAvailable)
                                            class="rounded-2xl border px-3 py-1.5 text-xs font-semibold transition {{ $selectedColor === $color ? 'border-[#b69066] bg-[#b69066]/10 text-[#7a5d3d] ring-2 ring-[#b69066]/15' : 'border-stone-200 bg-white text-stone-700' }} {{ $isColorAvailable ? 'hover:border-[#b69066] hover:bg-[#faf6f1]' : 'opacity-40 cursor-not-allowed' }}">
                                            {{ $color }}
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-600">
                                    Sản phẩm này chưa có dữ liệu màu riêng.
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-gray-500">Chọn size</h2>
                                <span
                                    class="text-xs font-semibold text-[#7a5d3d] bg-[#b69066]/12 px-3 py-1 rounded-full">
                                    {{ $selectedSize !== '' ? $selectedSize : 'Chưa chọn' }}
                                </span>
                            </div>

                            @if (!empty($sizeOptions))
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($sizeOptions as $size)
                                        @php
                                            $isSizeAvailable = $variants->contains(function ($variant) use (
                                                $size,
                                                $selectedColor,
                                            ) {
                                                if ((int) ($variant->stock ?? 0) < 1) {
                                                    return false;
                                                }

                                                if (
                                                    strtoupper(trim((string) ($variant->size ?? ''))) !==
                                                    strtoupper(trim((string) $size))
                                                ) {
                                                    return false;
                                                }

                                                if ($selectedColor === '') {
                                                    return true;
                                                }

                                                return trim((string) ($variant->color ?? '')) === $selectedColor;
                                            });
                                        @endphp
                                        <button type="button" wire:click="selectSize(@js($size))"
                                            @disabled(!$isSizeAvailable)
                                            class="min-w-9 rounded-2xl border px-3 py-1.5 text-xs font-bold transition {{ $selectedSize === $size ? 'border-[#b69066] bg-[#b69066]/10 text-[#7a5d3d] ring-2 ring-[#b69066]/15' : 'border-stone-200 bg-white text-stone-700' }} {{ $isSizeAvailable ? 'hover:border-[#b69066] hover:bg-[#faf6f1]' : 'opacity-40 cursor-not-allowed' }}">
                                            {{ $size }}
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-600">
                                    Sản phẩm này chưa có dữ liệu size riêng.
                                </div>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-stone-200 bg-stone-50 p-3 text-xs text-stone-700">
                            @if ($selectedSku)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="font-semibold text-gray-900">SKU đang chọn</span>
                                    <span class="font-bold text-[#7a5d3d]">{{ $selectedSku->sku }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between gap-3">
                                    <span>Biến thể</span>
                                    <span>{{ $selectedSku->size ?: 'N/A' }} @if ($selectedSku->color)
                                            • {{ $selectedSku->color }}
                                        @endif
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center justify-between gap-3">
                                    <span>Tồn kho</span>
                                    <span>{{ number_format((int) $selectedSku->stock) }} sản phẩm</span>
                                </div>
                            @else
                                <p>Vui lòng chọn đủ màu và size để xác định biến thể khả dụng.</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div class="rounded-2xl bg-stone-50 border border-stone-200 p-3">
                            <p
                                class="inline-flex items-center gap-1.5 text-xs uppercase tracking-wide text-slate-500 font-semibold">
                                <i class="ri-truck-line text-sm"></i>Giao hàng
                            </p>
                            <p class="mt-2 font-bold text-slate-900">2 - 4 ngày</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 border border-stone-200 p-3">
                            <p
                                class="inline-flex items-center gap-1.5 text-xs uppercase tracking-wide text-slate-500 font-semibold">
                                <i class="ri-refresh-line text-sm"></i>Đổi trả
                            </p>
                            <p class="mt-2 font-bold text-slate-900">Trong 7 ngày</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 border border-stone-200 p-3">
                            <p
                                class="inline-flex items-center gap-1.5 text-xs uppercase tracking-wide text-slate-500 font-semibold">
                                <i class="ri-shield-check-line text-sm"></i>Hỗ trợ
                            </p>
                            <p class="mt-2 font-bold text-slate-900">Thanh toán an toàn</p>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-gray-500">Số lượng</h2>
                            @if ($selectedSku)
                                <span class="text-xs text-gray-500">Tối đa
                                    {{ number_format((int) $selectedSku->stock) }} sản phẩm</span>
                            @endif
                        </div>

                        <div
                            class="inline-flex items-center rounded-2xl border border-gray-200 bg-white overflow-hidden">
                            <button type="button" wire:click="decreaseQuantity"
                                class="h-9 w-9 flex items-center justify-center text-stone-700 hover:bg-stone-50 transition">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <input type="number" min="1" wire:model.live="selectedQuantity"
                                class="h-9 w-14 border-x border-stone-200 text-center text-xs font-semibold focus:outline-none focus:bg-stone-50">
                            <button type="button" wire:click="increaseQuantity"
                                class="h-9 w-9 flex items-center justify-center text-stone-700 hover:bg-stone-50 transition">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-1">
                        <button type="button" wire:click="addToCart" wire:loading.attr="disabled"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-[#b69066] px-3.5 py-2.5 text-xs md:text-sm font-bold text-white shadow-lg shadow-[#b69066]/20 transition hover:-translate-y-px hover:shadow-xl hover:shadow-[#b69066]/25 hover:bg-[#a27d58] disabled:opacity-70">
                            <i class="ri-shopping-cart-line"></i>
                            Thêm vào giỏ hàng
                        </button>
                        <button type="button" wire:click="toggleWhistlist" wire:loading.attr="disabled"
                            class="h-10 w-10 shrink-0 rounded-2xl border transition hover:bg-stone-50 disabled:opacity-70 {{ $isWishlisted ? 'border-rose-200 bg-rose-50 text-rose-500' : 'border-stone-200 bg-white text-stone-600' }}">
                            <i class="{{ $isWishlisted ? 'ri-heart-fill' : 'ri-heart-line' }} text-lg"></i>
                        </button>
                    </div>

                    <div class="rounded-3xl border border-stone-200 bg-stone-50 p-3.5 space-y-2">
                        <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-gray-500">Mô tả sản phẩm</h2>
                        <p class="text-sm leading-6 text-gray-700 whitespace-pre-line">
                            {{ $product->description ?: 'Sản phẩm chưa có mô tả chi tiết.' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-7 grid grid-cols-1 lg:grid-cols-[0.95fr_1.05fr] gap-4">
            <div
                class="rounded-4xl bg-white border border-gray-100 shadow-[0_12px_36px_rgba(15,23,42,0.06)] p-4.5 md:p-5 ring-1 ring-stone-100/60">
                <div class="flex items-center justify-between gap-3 mb-4.5">
                    <h2 class="text-lg md:text-xl font-black text-gray-900">Thông tin sản phẩm</h2>
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Chi tiết</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-3.5 shadow-sm">
                        <p class="text-gray-500 font-semibold uppercase tracking-wide text-xs">Mã sản phẩm</p>
                        <p class="mt-1 font-bold text-gray-900">{{ $product->product_code }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-3.5 shadow-sm">
                        <p class="text-gray-500 font-semibold uppercase tracking-wide text-xs">Danh mục</p>
                        <p class="mt-1 font-bold text-gray-900">{{ $product->category?->name ?? 'Chưa phân loại' }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-3.5 shadow-sm">
                        <p class="text-gray-500 font-semibold uppercase tracking-wide text-xs">Bộ sưu tập</p>
                        <p class="mt-1 font-bold text-gray-900">
                            {{ $product->collection?->name ?? 'Chưa gắn bộ sưu tập' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-3.5 shadow-sm">
                        <p class="text-gray-500 font-semibold uppercase tracking-wide text-xs">Tình trạng</p>
                        <p class="mt-1 font-bold text-gray-900">{{ $product->is_active ? 'Đang bán' : 'Ngưng bán' }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-4xl bg-white border border-gray-100 shadow-[0_12px_36px_rgba(15,23,42,0.06)] p-4.5 md:p-5 ring-1 ring-stone-100/60">
                <div class="flex items-center justify-between gap-3 mb-4.5">
                    <h2 class="text-lg md:text-xl font-black text-gray-900">Đánh giá từ khách hàng</h2>
                    <span class="text-sm text-gray-500">{{ number_format($reviewCount) }} lượt đánh giá</span>
                </div>

                <div
                    class="mb-4.5 flex items-center gap-3 rounded-2xl bg-amber-50 border border-amber-100 p-3 shadow-sm">
                    <div class="text-2xl md:text-3xl font-black text-amber-500">{{ number_format($averageRating, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 text-lg">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= round($averageRating) ? 'ri-star-fill' : 'ri-star-line' }}"></i>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Điểm trung bình từ khách hàng đã mua</p>
                    </div>
                </div>

                <div class="space-y-3.5">
                    @forelse($reviews as $review)
                        <article
                            class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3.5 shadow-sm hover:border-stone-200 transition">
                            <div class="flex flex-wrap items-start justify-between gap-2.5 mb-2.5">
                                <div>
                                    <p class="font-bold text-gray-900">
                                        {{ $review->user?->full_name ?: $review->user?->username ?: 'Ẩn danh' }}
                                    </p>
                                    @if ($review->order)
                                        <p class="mt-0.5 text-xs font-semibold text-gray-500">
                                            Từ đơn {{ $review->order->order_code }}
                                        </p>
                                    @endif
                                    <div class="mt-1 flex items-center gap-1 text-amber-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="{{ $i <= (int) $review->rating ? 'ri-star-fill' : 'ri-star-line text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-semibold text-gray-500">{{ $review->created_at?->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-sm leading-6 text-gray-700">{{ $review->content }}</p>

                            @if ($review->admin_reply)
                                <div class="mt-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3">
                                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-emerald-700">
                                        Phản hồi của shop
                                    </p>
                                    <p class="mt-2 text-sm leading-6 text-emerald-900">
                                        {{ $review->admin_reply }}
                                    </p>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div
                            class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-gray-500">
                            Chưa có đánh giá nào cho sản phẩm này.
                        </div>
                    @endforelse
                </div>

                <div class="mt-5">
                    {{ $reviews->links() }}
                </div>
            </div>
        </section>

        @if ($relatedProducts->count())
            <section class="mt-7">
                <div class="flex items-end justify-between gap-3 mb-4.5">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-stone-400">Khám phá thêm
                        </p>
                        <h2 class="text-lg md:text-xl font-black text-gray-900">Sản phẩm liên quan</h2>
                        <p class="text-sm text-gray-500 mt-1">Gợi ý từ cùng danh mục để bạn khám phá thêm.</p>
                    </div>

                    <div class="hidden sm:flex items-center gap-2">
                        <button type="button" @click="prev()"
                            class="h-8 w-8 inline-flex items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 hover:border-[#b69066] hover:text-[#7a5d3d] transition"
                            aria-label="Trước đó">
                            <i class="ri-arrow-left-s-line"></i>
                        </button>
                        <button type="button" @click="next()"
                            class="h-8 w-8 inline-flex items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 hover:border-[#b69066] hover:text-[#7a5d3d] transition"
                            aria-label="Tiếp theo">
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>
                </div>

                <div x-data="{
                    index: 0,
                    perView: 1,
                    total: {{ $relatedProducts->count() }},
                    timer: null,
                    setupResize: null,
                    init() {
                        this.updatePerView();
                        this.setupResize = () => this.updatePerView();
                        window.addEventListener('resize', this.setupResize);
                        this.startAuto();
                    },
                    destroy() {
                        this.stopAuto();
                        if (this.setupResize) {
                            window.removeEventListener('resize', this.setupResize);
                        }
                    },
                    updatePerView() {
                        const width = window.innerWidth;
                        this.perView = width >= 1280 ? 4 : (width >= 640 ? 2 : 1);
                        if (this.index > this.maxIndex()) {
                            this.index = 0;
                        }
                    },
                    maxIndex() {
                        return Math.max(this.total - this.perView, 0);
                    },
                    next() {
                        this.index = this.index >= this.maxIndex() ? 0 : this.index + 1;
                    },
                    prev() {
                        this.index = this.index <= 0 ? this.maxIndex() : this.index - 1;
                    },
                    startAuto() {
                        this.stopAuto();
                        if (this.total <= this.perView) {
                            return;
                        }
                        this.timer = setInterval(() => this.next(), 3200);
                    },
                    stopAuto() {
                        if (this.timer) {
                            clearInterval(this.timer);
                            this.timer = null;
                        }
                    }
                }" x-init="init()" @mouseenter="stopAuto()"
                    @mouseleave="startAuto()" class="space-y-3">
                    <div
                        class="overflow-hidden rounded-3xl bg-white border border-stone-100 shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
                        <div class="flex transition-transform duration-700 ease-out"
                            :style="`transform: translateX(-${index * (100 / perView)}%);`">
                            @foreach ($relatedProducts as $relatedProduct)
                                <div class="w-full sm:w-1/2 xl:w-1/4 shrink-0 px-1.5">
                                    <x-user.product-card :product="$relatedProduct" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-1.5" x-show="total > perView">
                        <template x-for="dot in (maxIndex() + 1)" :key="dot">
                            <button type="button" @click="index = dot - 1"
                                class="h-1.5 rounded-full transition-all duration-300"
                                :class="index === dot - 1 ? 'w-5 bg-[#b69066]' : 'w-2.5 bg-stone-300'"
                                aria-label="Chuyển slide"></button>
                        </template>
                    </div>
                </div>
            </section>
        @endif
    </div>
</div>
