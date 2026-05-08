<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Header with Gradient -->
    <div class="relative bg-gradient-to-r from-[#f6efe4] via-white to-[#f6efe4] border-b border-gray-200 shadow-sm">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#bc9c75] rounded-full -mr-48 -mt-48"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-[#bc9c75] rounded-full -ml-36 -mb-36"></div>
        </div>

        <div class="relative max-w-[1400px] mx-auto px-4 py-12">
            <!-- Back Button & Breadcrumb -->
            <div class="flex items-center gap-4 mb-6">
                <a wire:navigate href="{{ route('user.collections') }}"
                    class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white border-2 border-gray-200 hover:border-[#bc9c75] hover:bg-[#bc9c75] hover:text-white transition-all duration-300 group">
                    <i class="ri-arrow-left-line text-lg group-hover:scale-110 transition-transform"></i>
                </a>
                <nav class="text-sm text-gray-500 flex items-center gap-2">
                    <a wire:navigate href="{{ route('dashboard') }}"
                        class="hover:text-[#bc9c75] transition flex items-center gap-1">
                        <i class="ri-home-line"></i>Trang chủ
                    </a>
                    <span class="text-gray-300">/</span>
                    <a wire:navigate href="{{ route('user.collections') }}" class="hover:text-[#bc9c75] transition">Bộ
                        Sưu Tập</a>
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-900 font-semibold">{{ $collection->name }}</span>
                </nav>
            </div>

            <!-- Title Section -->
            <div class="flex items-start justify-between gap-8">
                <div class="flex-1">
                    <h1 class="text-5xl font-black text-gray-900 mb-4 leading-tight">
                        {{ $collection->name }}
                    </h1>
                    @if ($collection->description)
                        <p class="text-gray-600 text-lg max-w-2xl leading-relaxed">
                            {{ $collection->description }}
                        </p>
                    @endif
                </div>

                @if ($collection->thumbnail_url)
                    <div class="hidden lg:block flex-shrink-0">
                        <img src="{{ asset('storage/' . $collection->thumbnail_url) }}" alt="{{ $collection->name }}"
                            class="w-48 h-48 object-cover rounded-2xl shadow-xl">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-[1400px] mx-auto px-4 py-8">
        <div class="flex gap-8">
            <!-- Desktop Sidebar Filter -->
            <aside class="w-72 shrink-0 hidden lg:block">
                <div class="sticky top-24 bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-8">
                    <!-- Filter Header -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="ri-filter-3-line mr-2"></i>Bộ Lọc
                        </h3>
                        @if ($sort !== 'newest' || $priceRange !== 'all' || !empty($selectedSizes))
                            <button wire:click="resetFilters"
                                class="text-xs font-semibold text-[#bc9c75] hover:text-[#a68560] transition">
                                Xoá
                            </button>
                        @endif
                    </div>

                    <div class="h-px bg-gray-100"></div>

                    <!-- Sort Filter -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-700 mb-4">
                            Sắp xếp theo
                        </label>
                        <select wire:model.live="sort"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#bc9c75] focus:border-transparent text-sm font-medium transition-all bg-white hover:border-gray-300">
                            <option value="newest">Mới nhất</option>
                            <option value="popular">Phổ biến nhất</option>
                            <option value="price-asc">Giá: Thấp → Cao</option>
                            <option value="price-desc">Giá: Cao → Thấp</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-700 mb-4">
                            Mức giá
                        </label>
                        <div class="space-y-3">
                            @foreach ([
        'all' => 'Tất cả mức giá',
        'under-500' => 'Dưới 500K',
        '500-1000' => '500K - 1M',
        '1000-2000' => '1M - 2M',
        'above-2000' => 'Trên 2M',
    ] as $value => $label)
                                <label
                                    class="flex items-center gap-3 cursor-pointer group p-2 hover:bg-gray-50 rounded-lg transition">
                                    <input type="radio" wire:model.live="priceRange" value="{{ $value }}"
                                        class="w-4 h-4 accent-[#bc9c75] cursor-pointer">
                                    <span
                                        class="text-sm text-gray-700 group-hover:text-[#bc9c75] transition font-medium">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Size Filter -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-700 mb-4">
                            Kích thước
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $size)
                                <label
                                    class="relative flex items-center justify-center border-2 border-gray-200 py-3 rounded-lg cursor-pointer hover:border-[#bc9c75] transition-all group">
                                    <input type="checkbox" wire:model.live="selectedSizes" value="{{ $size }}"
                                        class="absolute opacity-0 peer">
                                    <span
                                        class="text-sm font-bold peer-checked:text-[#bc9c75] peer-checked:font-black group-hover:text-[#bc9c75] transition">
                                        {{ $size }}
                                    </span>
                                    <div
                                        class="absolute inset-0 border-2 border-transparent peer-checked:border-[#bc9c75] rounded-lg pointer-events-none">
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <button wire:click="resetFilters"
                        class="w-full bg-gradient-to-r from-[#bc9c75] to-[#a68560] text-white py-3 rounded-lg text-sm font-bold uppercase tracking-wide hover:shadow-lg transition-all duration-300 active:scale-95">
                        <i class="ri-refresh-line mr-2"></i>Đặt lại bộ lọc
                    </button>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Mobile Filter Button -->
                <div class="lg:hidden mb-6">
                    <button onclick="toggleMobileFilters()"
                        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-[#bc9c75] to-[#a68560] text-white py-3 rounded-xl font-bold uppercase tracking-wide shadow-md hover:shadow-lg transition-all active:scale-95">
                        <i class="ri-filter-3-line text-lg"></i>
                        Bộ Lọc
                    </button>
                </div>

                <!-- Mobile Filters Modal -->
                <div id="mobile-filters" class="hidden fixed inset-0 bg-black/50 z-50 lg:hidden"
                    onclick="if(event.target.id === 'mobile-filters') toggleMobileFilters()">
                    <div
                        class="fixed left-0 top-0 h-full w-80 bg-white p-6 overflow-y-auto space-y-6 animate-slide-in-left shadow-2xl">
                        <div class="flex justify-between items-center mb-4 pb-4 border-b-2 border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="ri-filter-3-line mr-2"></i>Bộ Lọc
                            </h3>
                            <button onclick="toggleMobileFilters()"
                                class="text-2xl text-gray-500 hover:text-gray-700 transition">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-700 mb-3">Sắp
                                xếp</label>
                            <select wire:model.live="sort"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="newest">Mới nhất</option>
                                <option value="popular">Phổ biến</option>
                                <option value="price-asc">Giá ↑</option>
                                <option value="price-desc">Giá ↓</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-700 mb-3">Mức
                                giá</label>
                            <select wire:model.live="priceRange"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="all">Tất cả</option>
                                <option value="under-500">Dưới 500K</option>
                                <option value="500-1000">500K - 1M</option>
                                <option value="1000-2000">1M - 2M</option>
                                <option value="above-2000">Trên 2M</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-700 mb-3">Kích
                                thước</label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $size)
                                    <label
                                        class="relative flex items-center justify-center border border-gray-300 py-2 rounded cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectedSizes"
                                            value="{{ $size }}" class="absolute opacity-0 peer">
                                        <span
                                            class="text-xs font-bold peer-checked:text-[#bc9c75]">{{ $size }}</span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-[#bc9c75] rounded pointer-events-none">
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button wire:click="resetFilters"
                            class="w-full bg-gray-200 text-gray-800 py-2 rounded-lg text-sm font-bold hover:bg-gray-300 transition">
                            <i class="ri-refresh-line mr-1"></i>Xoá bộ lọc
                        </button>
                    </div>
                </div>

                <!-- Top Bar with Info and Sort -->
                <div
                    class="flex items-center justify-between mb-8 p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div>
                        <p class="text-sm text-gray-600">
                            <span class="font-bold text-gray-900">{{ $products->total() }}</span> sản phẩm
                            @if ($sort !== 'newest')
                                <span class="text-gray-500">• Sắp xếp: <span class="font-semibold text-[#bc9c75]">
                                        @php
                                            $sortLabels = [
                                                'newest' => 'Mới nhất',
                                                'popular' => 'Phổ biến',
                                                'price-asc' => 'Giá ↑',
                                                'price-desc' => 'Giá ↓',
                                            ];
                                            echo $sortLabels[$sort] ?? '';
                                        @endphp
                                    </span></span>
                            @endif
                        </p>
                    </div>

                    <div class="hidden md:flex gap-2">
                        @foreach (['newest' => 'Mới', 'popular' => 'Hot', 'price-asc' => 'Rẻ', 'price-desc' => 'Đắt'] as $sortVal => $label)
                            <button wire:click="$set('sort', '{{ $sortVal }}')"
                                class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition-all
                                {{ $sort === $sortVal ? 'bg-[#bc9c75] text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Product Grid -->
                @if ($products->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                        @foreach ($products as $product)
                            <x-user.product-card :product="$product" />
                        @endforeach
                    </div>

                    <!-- Premium Pagination -->
                    <div class="flex justify-center items-center gap-3 mt-16 mb-12">
                        {{ $products->links('pagination::tailwind') }}
                    </div>
                @else
                    <!-- Premium Empty State -->
                    <div class="col-span-full flex items-center justify-center py-24">
                        <div class="text-center">
                            <div class="mb-6 relative w-32 h-32 mx-auto">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-[#bc9c75] to-[#a68560] opacity-10 rounded-full">
                                </div>
                                <i
                                    class="ri-inbox-line text-7xl text-gray-300 absolute inset-0 flex items-center justify-center"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Không tìm thấy sản phẩm</h3>
                            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                                Bộ sưu tập này hiện chưa có sản phẩm phù hợp với bộ lọc của bạn. Hãy thử đặt lại bộ lọc!
                            </p>
                            <button wire:click="resetFilters"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-[#bc9c75] to-[#a68560] text-white font-bold py-3 px-8 rounded-full hover:shadow-lg transition-all duration-300 active:scale-95">
                                <i class="ri-refresh-line"></i>
                                Đặt lại bộ lọc
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleMobileFilters() {
                const modal = document.getElementById('mobile-filters');
                modal.classList.toggle('hidden');
            }
        </script>
    @endpush

    @push('styles')
        <style>
            @keyframes slideInLeft {
                from {
                    transform: translateX(-100%);
                }

                to {
                    transform: translateX(0);
                }
            }

            .animate-slide-in-left {
                animation: slideInLeft 0.3s ease-in-out;
            }
        </style>
    @endpush
</div>
