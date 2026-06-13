
<div class="bg-white min-h-screen">
    
    <div id="sidebar-overlay" onclick="toggleSidebar(false)"
    class="fixed inset-0 bg-black/50 z-100 hidden transition-opacity duration-300"></div>
    
    <div class="max-w-[1400px] mx-auto px-4 py-8">
                    <nav class="text-sm text-gray-600 mb-4">
                        <a href="{{ route('dashboard') }}" class="hover:text-[#bc9c75] transition">Trang chủ</a>
                        <span class="mx-2">/</span>
                        <span class="text-gray-900 font-semibold">Sản phẩm</span>
                    </nav>
                </div>
    <div class="max-w-350 mx-auto py-10 px-4 flex flex-col gap-10">
        <livewire:user.voucher-offers :key="'voucher-offers-product-page'" />
        
        <div class="md:hidden mb-2">
            <button onclick="toggleSidebar(true)"
                class="flex items-center justify-center gap-2 w-full bg-[#fdf2f2] border border-[#f4dfdf] py-3 rounded-xl font-bold text-sm uppercase tracking-wider text-gray-700 active:scale-95 transition-all duration-200 hover:bg-[#fbeaea]">
                <i class="ri-filter-3-line text-lg"></i>
                Bộ lọc
            </button>
        </div>

        <div class="flex gap-10">
            <aside id="main-sidebar"
                class="fixed top-17.5 left-0 h-[calc(100vh-70px)] w-[82%] max-w-[320px] bg-white z-101 p-6 shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto md:relative md:translate-x-0 md:w-64 md:max-w-none md:shrink-0 md:z-auto md:shadow-none md:p-0 md:block md:top-28 md:h-fit md:space-y-10">

                <div>
                    <div class="filter-group mb-8">
                        <h3
                            class="font-bold border-b border-gray-100 pb-3 mb-6 uppercase text-sm tracking-widest text-gray-900">
                            Tìm kiếm
                        </h3>
                        <input type="text" wire:model.live="search" placeholder="Nhập tên sản phẩm..."
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#bc9c75]">
                    </div>

                    <h3
                        class="font-bold uppercase text-sm tracking-widest text-gray-900 mb-6 pb-3 border-b border-gray-100">
                        Danh mục
                    </h3>

                    <div class="space-y-4 pl-1 text-sm text-gray-700 font-medium">
                        @forelse($categoryTree as $node)
                            <div>
                                <label class="flex items-center gap-3 cursor-pointer py-1.5">
                                    <input type="checkbox" wire:click="toggleCategory({{ $node['parent']->id }})"
                                        @if (in_array($node['parent']->id, $this->selectedCategories)) checked @endif
                                        class="accent-[#c5a059] w-4 h-4 rounded">
                                    <span
                                        class="font-semibold hover:text-[#bc9c75] transition-colors">{{ $node['parent']->name }}</span>
                                </label>

                                @if ($node['children']->isNotEmpty())
                                    <ul class="mt-1 ml-7 space-y-1.5 border-l border-[#efe6d9] pl-3">
                                        @foreach ($node['children'] as $child)
                                            <li>
                                                <label class="flex items-center gap-3 cursor-pointer py-1">
                                                    <input type="checkbox"
                                                        wire:click="toggleCategory({{ $child->id }})"
                                                        @if (in_array($child->id, $this->selectedCategories)) checked @endif
                                                        class="accent-[#c5a059] w-4 h-4 rounded">
                                                    <span
                                                        class="hover:text-[#bc9c75] transition-colors">{{ $child->name }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 py-1">Không có danh mục</p>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-10 mt-10 md:mt-0">
                    <div class="filter-group">
                        <h3
                            class="font-bold border-b border-gray-100 pb-3 mb-6 uppercase text-sm tracking-widest text-gray-900">
                            Chọn mức giá
                        </h3>
                        <div class="space-y-3 text-sm text-gray-700">
                            @foreach ($priceRangeOptions as $priceKey => $priceOption)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" wire:model.live="priceRange" value="{{ $priceKey }}"
                                        class="accent-[#c5a059] w-4 h-4 rounded">
                                    <span
                                        class="group-hover:text-[#c5a059] transition-colors">{{ $priceOption['label'] }}</span>
                                </label>
                            @endforeach

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" wire:model.live="priceRange" value="all"
                                    class="accent-[#c5a059] w-4 h-4 rounded">
                                <span class="group-hover:text-[#c5a059] transition-colors">Tất cả mức giá</span>
                            </label>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h3
                            class="font-bold border-b border-gray-100 pb-3 mb-6 uppercase text-sm tracking-widest text-gray-900">
                            Kích thước
                        </h3>
                        @if (!empty($sizeOptions))
                            <div class="grid grid-cols-4 gap-2 text-xs font-bold">
                                @foreach ($sizeOptions as $size)
                                    <label
                                        class="relative flex items-center justify-center border border-gray-200 py-2 rounded cursor-pointer hover:border-[#c5a059] transition-all group">
                                        <input type="checkbox" wire:click="toggleSize('{{ $size }}')"
                                            @if (in_array($size, $this->sizes, true)) checked @endif
                                            class="absolute opacity-0 peer">
                                        <span
                                            class="peer-checked:text-[#c5a059] group-hover:text-[#c5a059]">{{ $size }}</span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-[#c5a059] rounded pointer-events-none">
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Chưa có dữ liệu size.</p>
                        @endif
                    </div>

                </div>
            </aside>

            <div class="flex-1 min-w-0">
                @php
                    $categoryNameMap = collect($categoryOptions)->pluck('name', 'id');
                @endphp

                <div
                    class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-8 border border-[#efe7db] bg-[#faf8f5] p-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-gray-500 shadow-sm">
                    <div class="flex gap-6 items-center overflow-x-auto whitespace-nowrap">
                        @foreach ($sortOptions as $sortKey => $sortLabel)
                            <button type="button" wire:click="setSort('{{ $sortKey }}')"
                                class="pb-1 border-b-2 transition-all duration-200 {{ $sort === $sortKey ? 'text-black border-[#c5a059]' : 'border-transparent hover:text-[#c5a059]' }}">
                                {{ $sortLabel }}
                            </button>
                        @endforeach
                        <span class="text-gray-400">{{ $products->total() }} sản phẩm</span>
                    </div>

                    <select wire:model.live="sort"
                        class="px-3 py-2 border border-[#e8dece] bg-white rounded-xl text-[11px] text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/40 shadow-sm">
                        @foreach ($sortOptions as $sortKey => $sortLabel)
                            <option value="{{ $sortKey }}">{{ $sortLabel }}</option>
                        @endforeach
                    </select>
                </div>

                @php
                    $hasActiveFilters =
                        trim($search) !== '' ||
                        $sort !== 'newest' ||
                        $priceRange !== 'all' ||
                        !empty($this->selectedCategories) ||
                        !empty($sizes);
                @endphp

                <div class="mb-7 flex flex-wrap items-center gap-2">
                    @if (trim($search) !== '')
                        <button type="button" wire:click="$set('search', '')"
                            class="group inline-flex items-center gap-2 rounded-full border border-[#eadfce] bg-white px-3 py-1.5 text-[11px] font-semibold text-gray-700 shadow-sm hover:border-[#bc9c75] hover:text-[#9d7a4a] transition-all duration-200">
                            <span>Tìm: {{ $search }}</span>
                            <i class="ri-close-line text-sm"></i>
                        </button>
                    @endif

                    @if ($sort !== 'newest')
                        <button type="button" wire:click="setSort('newest')"
                            class="group inline-flex items-center gap-2 rounded-full border border-[#eadfce] bg-white px-3 py-1.5 text-[11px] font-semibold text-gray-700 shadow-sm hover:border-[#bc9c75] hover:text-[#9d7a4a] transition-all duration-200">
                            <span>Sắp xếp: {{ $sortOptions[$sort] ?? 'Mặc định' }}</span>
                            <i class="ri-close-line text-sm"></i>
                        </button>
                    @endif

                    @if ($priceRange !== 'all')
                        <button type="button" wire:click="$set('priceRange', 'all')"
                            class="group inline-flex items-center gap-2 rounded-full border border-[#eadfce] bg-white px-3 py-1.5 text-[11px] font-semibold text-gray-700 shadow-sm hover:border-[#bc9c75] hover:text-[#9d7a4a] transition-all duration-200">
                            <span>Giá: {{ $priceRangeOptions[$priceRange]['label'] ?? 'Tất cả mức giá' }}</span>
                            <i class="ri-close-line text-sm"></i>
                        </button>
                    @endif

                    @foreach ($categoryOptions as $category)
                        @if (in_array($category->id, $this->selectedCategories))
                            <button type="button" wire:click="toggleCategory({{ $category->id }})"
                                class="group inline-flex items-center gap-2 rounded-full border border-[#eadfce] bg-white px-3 py-1.5 text-[11px] font-semibold text-gray-700 shadow-sm hover:border-[#bc9c75] hover:text-[#9d7a4a] transition-all duration-200">
                                <span>Danh mục: {{ $categoryNameMap[$category->id] }}</span>
                                <i class="ri-close-line text-sm"></i>
                            </button>
                        @endif
                    @endforeach

                    @foreach ($sizes as $size)
                        <button type="button" wire:click="toggleSize('{{ $size }}')"
                            class="group inline-flex items-center gap-2 rounded-full border border-[#eadfce] bg-white px-3 py-1.5 text-[11px] font-semibold text-gray-700 shadow-sm hover:border-[#bc9c75] hover:text-[#9d7a4a] transition-all duration-200">
                            <span>Size: {{ $size }}</span>
                            <i class="ri-close-line text-sm"></i>
                        </button>
                    @endforeach

                    @if ($hasActiveFilters)
                        <button type="button" wire:click="resetFilters"
                            class="inline-flex items-center gap-2 rounded-full border border-[#ffdbdb] bg-[#fff1f1] px-3 py-1.5 text-[11px] font-semibold text-[#d74b4b] shadow-sm hover:bg-[#ffe7e7] transition-all duration-200">
                            <i class="ri-delete-bin-line text-sm"></i>
                            Xóa tất cả
                        </button>
                    @endif

                    @if (!$hasActiveFilters)
                        <span class="text-xs text-gray-400 italic">Chưa có bộ lọc nào được chọn</span>
                    @endif
                </div>

                @if ($products->count())
                    <div id="product-grid" data-source="server" wire:loading.class="opacity-70"
                        wire:target="search,sort,priceRange,selectedCategories,sizes"
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
                        @foreach ($products as $product)
                            <x-user.product-card :product="$product" />
                        @endforeach
                    </div>

                    <div id="pagination-container" class="flex justify-center items-center gap-2 mt-12 mb-10">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-20 border border-dashed border-gray-200 rounded-2xl bg-gray-50/30">
                        <i class="ri-inbox-line text-6xl text-gray-300 mb-4 block"></i>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Không tìm thấy sản phẩm</h3>
                        <p class="text-gray-500">Vui lòng thử lại với từ khoá khác hoặc xoá bộ lọc</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar(open) {
            const sidebar = document.getElementById('main-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (!sidebar || !overlay) {
                return;
            }

            if (open) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function initProductAccordion() {
            const items = document.querySelectorAll('#main-sidebar .accordion-item');

            items.forEach((item, index) => {
                const header = item.querySelector('.accordion-header');
                const content = item.querySelector('.accordion-content');
                const icon = item.querySelector('.accordion-icon');

                if (!header || !content || !icon || header.dataset.binded === 'true') {
                    return;
                }

                if (index === 0) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    icon.classList.remove('ri-add-line');
                    icon.classList.add('ri-subtract-line');
                }

                header.dataset.binded = 'true';
                header.addEventListener('click', () => {
                    const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';

                    if (isOpen) {
                        content.style.maxHeight = '0px';
                        icon.classList.remove('ri-subtract-line');
                        icon.classList.add('ri-add-line');
                    } else {
                        content.style.maxHeight = content.scrollHeight + 'px';
                        icon.classList.remove('ri-add-line');
                        icon.classList.add('ri-subtract-line');
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initProductAccordion);
        document.addEventListener('livewire:navigated', initProductAccordion);
        document.addEventListener('livewire:init', () => {
            if (window.Livewire && typeof window.Livewire.hook === 'function') {
                window.Livewire.hook('morph.updated', () => {
                    initProductAccordion();
                });
            }
        });
    </script>
</div>
