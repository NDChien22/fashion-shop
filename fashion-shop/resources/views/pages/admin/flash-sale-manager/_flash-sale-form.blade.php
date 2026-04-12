@php
    $isEdit = isset($flashSale) && $flashSale;
    $formAction = $isEdit ? route('admin.update-flash-sale', $flashSale) : route('admin.store-flash-sale');
    $normalizeDate = function ($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '';
    };

    $name = old('name', $flashSale->name ?? '');
    $discountType = old('discount_type', $flashSale->discount_type ?? 'percent');
    $discountValue = old('discount_value', $flashSale->discount_value ?? '');
    $scope = old('scope', $flashSale->scope ?? 'all');
    $categoryId = old('category_id', $flashSale->category_id ?? '');
    $collectionId = old('collection_id', $flashSale->collection_id ?? '');
    $productId = old('product_id', $flashSale->product_id ?? '');
    $usageLimit = old('usage_limit', $flashSale->usage_limit ?? '');
    $startDate = old(
        'start_date',
        isset($flashSale) && $flashSale->start_date ? $normalizeDate($flashSale->start_date) : now()->format('Y-m-d'),
    );
    $endDate = old(
        'end_date',
        isset($flashSale) && $flashSale->end_date
            ? $normalizeDate($flashSale->end_date)
            : now()->addDays(7)->format('Y-m-d'),
    );
    $isActive = (bool) old('is_active', isset($flashSale) ? $flashSale->is_active : true);

    $selectedTargetLabel = 'Toàn bộ sản phẩm';
    if ($scope === 'category') {
        $selectedTargetLabel =
            optional($categoryOptions->firstWhere('id', (int) $categoryId))->name ?? 'Danh mục chưa chọn';
    } elseif ($scope === 'collection') {
        $selectedTargetLabel =
            optional($collectionOptions->firstWhere('id', (int) $collectionId))->name ?? 'Bộ sưu tập chưa chọn';
    } elseif ($scope === 'product') {
        $selectedProduct = $productOptions->firstWhere('id', (int) $productId);
        $selectedTargetLabel = $selectedProduct
            ? trim(
                ($selectedProduct->product_code ? $selectedProduct->product_code . ' - ' : '') . $selectedProduct->name,
            )
            : 'Sản phẩm chưa chọn';
    }
@endphp

<div class="max-w-6xl mx-auto my-10 p-8 bg-white rounded-[40px] shadow-2xl shadow-gray-200/50 border border-gray-50">
    <form action="{{ $formAction }}" method="POST" class="space-y-8">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-gray-50/80 p-6 rounded-3xl border border-gray-100 space-y-5">
                    <p class="text-[10px] font-black text-[#bc9c75] uppercase tracking-widest flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded-full bg-[#bc9c75] text-white flex items-center justify-center text-[8px]">1</span>
                        Thông tin chiến dịch
                    </p>

                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Tên chương trình</label>
                        <input type="text" name="name" value="{{ $name }}"
                            placeholder="Ví dụ: Sale chào hè 2026"
                            class="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none text-sm font-semibold focus:border-[#bc9c75]/40">
                        @error('name')
                            <p class="text-xs text-red-500 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="relative">
                            <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Mức giảm</label>
                            <input type="number" name="discount_value" value="{{ $discountValue }}" min="0"
                                step="0.01" placeholder="0"
                                class="w-full pl-4 pr-20 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none font-black text-2xl text-red-500 focus:border-[#bc9c75]/40">
                            <select name="discount_type"
                                class="absolute right-3 top-7 bottom-3 bg-gray-50 border-none rounded-xl text-xs font-black px-3 cursor-pointer">
                                <option value="percent" @selected($discountType === 'percent')>%</option>
                                <option value="fixed" @selected($discountType === 'fixed')>đ</option>
                            </select>
                            @error('discount_value')
                                <p class="text-xs text-red-500 ml-2 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Giới hạn bán</label>
                            <input type="number" name="usage_limit" value="{{ $usageLimit }}" min="1"
                                placeholder="Không giới hạn"
                                class="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none text-sm font-semibold focus:border-[#bc9c75]/40">
                            @error('usage_limit')
                                <p class="text-xs text-red-500 ml-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Bắt đầu</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none text-[12px] font-semibold text-gray-600 focus:border-[#bc9c75]/40">
                            @error('start_date')
                                <p class="text-xs text-red-500 ml-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Kết thúc</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none text-[12px] font-semibold text-gray-600 focus:border-[#bc9c75]/40">
                            @error('end_date')
                                <p class="text-xs text-red-500 ml-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" @checked($isActive)
                            class="w-4 h-4 rounded border-gray-300 text-[#bc9c75] focus:ring-[#bc9c75]">
                        <span class="text-sm font-semibold text-gray-700">Kích hoạt chương trình ngay</span>
                    </label>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <p class="text-[10px] font-black text-[#bc9c75] uppercase tracking-widest flex items-center gap-2">
                    <span
                        class="w-5 h-5 rounded-full bg-[#bc9c75] text-white flex items-center justify-center text-[8px]">2</span>
                    Phạm vi áp dụng
                </p>

                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                    <label
                        class="apply-card p-4 bg-gray-50 rounded-3xl border-2 border-transparent transition-all flex flex-col items-center gap-2 group hover:bg-white hover:shadow-lg cursor-pointer">
                        <input type="radio" name="scope" value="all" class="sr-only"
                            @checked($scope === 'all')>
                        <div
                            class="scope-icon w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-300 group-hover:text-[#bc9c75] transition-all">
                            <i class="fa-solid fa-earth-asia text-sm"></i>
                        </div>
                        <span
                            class="scope-text font-bold text-[9px] uppercase text-gray-400 group-hover:text-gray-700">Toàn
                            bộ</span>
                    </label>

                    <label
                        class="apply-card p-4 bg-gray-50 rounded-3xl border-2 border-transparent transition-all flex flex-col items-center gap-2 group hover:bg-white hover:shadow-lg cursor-pointer">
                        <input type="radio" name="scope" value="category" class="sr-only"
                            @checked($scope === 'category')>
                        <div
                            class="scope-icon w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-300 group-hover:text-[#bc9c75] transition-all">
                            <i class="fa-solid fa-folder-tree text-sm"></i>
                        </div>
                        <span
                            class="scope-text font-bold text-[9px] uppercase text-gray-400 group-hover:text-gray-700">Danh
                            mục</span>
                    </label>

                    <label
                        class="apply-card p-4 bg-gray-50 rounded-3xl border-2 border-transparent transition-all flex flex-col items-center gap-2 group hover:bg-white hover:shadow-lg cursor-pointer">
                        <input type="radio" name="scope" value="collection" class="sr-only"
                            @checked($scope === 'collection')>
                        <div
                            class="scope-icon w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-300 group-hover:text-[#bc9c75] transition-all">
                            <i class="fa-solid fa-layer-group text-sm"></i>
                        </div>
                        <span
                            class="scope-text font-bold text-[9px] uppercase text-gray-400 group-hover:text-gray-700">Bộ
                            sưu
                            tập</span>
                    </label>

                    <label
                        class="apply-card p-4 bg-gray-50 rounded-3xl border-2 border-transparent transition-all flex flex-col items-center gap-2 group hover:bg-white hover:shadow-lg cursor-pointer">
                        <input type="radio" name="scope" value="product" class="sr-only"
                            @checked($scope === 'product')>
                        <div
                            class="scope-icon w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-300 group-hover:text-[#bc9c75] transition-all">
                            <i class="fa-solid fa-tag text-sm"></i>
                        </div>
                        <span
                            class="scope-text font-bold text-[9px] uppercase text-gray-400 group-hover:text-gray-700">Sản
                            phẩm</span>
                    </label>
                </div>

                <div class="bg-gray-50/60 rounded-3xl p-5 border border-gray-100 space-y-4">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Đối tượng cụ thể</div>

                    <div class="space-y-3" data-scope-panel="all">
                        <div
                            class="rounded-2xl border border-dashed border-gray-200 bg-white p-4 text-sm text-gray-500">
                            Chương trình áp dụng cho toàn bộ sản phẩm trong hệ thống.
                        </div>
                    </div>

                    <div class="space-y-3 hidden" data-scope-panel="category">
                        <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Chọn danh mục</label>
                        <select name="category_id" data-target-name="category"
                            class="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none text-sm font-semibold focus:border-[#bc9c75]/40">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach ($categoryOptions as $category)
                                <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-xs text-red-500 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3 hidden" data-scope-panel="collection">
                        <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Chọn bộ sưu tập</label>
                        <select name="collection_id" data-target-name="collection"
                            class="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none text-sm font-semibold focus:border-[#bc9c75]/40">
                            <option value="">-- Chọn bộ sưu tập --</option>
                            @foreach ($collectionOptions as $collection)
                                <option value="{{ $collection->id }}" @selected((string) $collectionId === (string) $collection->id)>
                                    {{ $collection->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('collection_id')
                            <p class="text-xs text-red-500 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3 hidden" data-scope-panel="product">
                        <label class="text-[9px] font-black text-gray-400 ml-2 uppercase">Chọn sản phẩm</label>
                        <select name="product_id" data-target-name="product"
                            class="w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm outline-none text-sm font-semibold focus:border-[#bc9c75]/40">
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach ($productOptions as $product)
                                <option value="{{ $product->id }}" @selected((string) $productId === (string) $product->id)>
                                    {{ $product->product_code ? $product->product_code . ' - ' : '' }}{{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-xs text-red-500 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-white p-4 text-sm text-gray-500">
                        Đối tượng đang chọn: <span id="selected-target-label"
                            class="font-semibold text-gray-800">{{ $selectedTargetLabel }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-50">
            <a href="{{ route('admin.flash-sale-manager') }}"
                class="px-8 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-gray-200 transition-all">
                Hủy bỏ
            </a>
            <button type="submit"
                class="px-10 py-3 bg-[#bc9c75] text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-[#bc9c75]/20 hover:scale-[1.02] active:scale-95 transition-all">
                {{ $isEdit ? 'Cập nhật flash sale' : 'Xác nhận tạo' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        function initFlashSaleScopePanels() {
            const form = document.querySelector('form[action*="flash-sale"]');

            if (!form || form.dataset.scopeInitDone === '1') {
                return;
            }

            form.dataset.scopeInitDone = '1';

            const scopeInputs = form.querySelectorAll('input[name="scope"]');
            const panels = form.querySelectorAll('[data-scope-panel]');
            const scopeCards = form.querySelectorAll('.apply-card');
            const selectedTargetLabel = form.querySelector('#selected-target-label');

            if (!scopeInputs.length || !panels.length) {
                return;
            }

            function syncScopePanels() {
                const selectedScope = form.querySelector('input[name="scope"]:checked')?.value || 'all';

                panels.forEach(function(panel) {
                    const isActive = panel.getAttribute('data-scope-panel') === selectedScope;
                    panel.classList.toggle('hidden', !isActive);

                    panel.querySelectorAll('select').forEach(function(select) {
                        select.disabled = !isActive;
                    });
                });

                scopeCards.forEach(function(card) {
                    const input = card.querySelector('input[name="scope"]');
                    const isActive = !!input && input.checked;
                    const icon = card.querySelector('.scope-icon');
                    const text = card.querySelector('.scope-text');

                    card.classList.toggle('border-[#bc9c75]', isActive);
                    card.classList.toggle('bg-white', isActive);
                    card.classList.toggle('shadow-lg', isActive);
                    card.classList.toggle('shadow-[#bc9c75]/10', isActive);

                    if (icon) {
                        icon.classList.toggle('text-[#bc9c75]', isActive);
                        icon.classList.toggle('text-gray-300', !isActive);
                    }

                    if (text) {
                        text.classList.toggle('text-gray-700', isActive);
                        text.classList.toggle('text-gray-400', !isActive);
                    }
                });

                if (selectedTargetLabel) {
                    const categorySelect = form.querySelector('select[name="category_id"]');
                    const collectionSelect = form.querySelector('select[name="collection_id"]');
                    const productSelect = form.querySelector('select[name="product_id"]');

                    let label = 'Toàn bộ sản phẩm';

                    if (selectedScope === 'category') {
                        label = categorySelect?.selectedOptions?.[0]?.text?.trim() || 'Danh mục chưa chọn';
                    } else if (selectedScope === 'collection') {
                        label = collectionSelect?.selectedOptions?.[0]?.text?.trim() || 'Bộ sưu tập chưa chọn';
                    } else if (selectedScope === 'product') {
                        label = productSelect?.selectedOptions?.[0]?.text?.trim() || 'Sản phẩm chưa chọn';
                    }

                    selectedTargetLabel.textContent = label;
                }
            }

            scopeInputs.forEach(function(input) {
                input.addEventListener('change', syncScopePanels);
            });

            form.querySelectorAll('select[name="category_id"], select[name="collection_id"], select[name="product_id"]')
                .forEach(
                    function(select) {
                        select.addEventListener('change', syncScopePanels);
                    },
                );

            syncScopePanels();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFlashSaleScopePanels);
        } else {
            initFlashSaleScopePanels();
        }
    </script>
@endpush
