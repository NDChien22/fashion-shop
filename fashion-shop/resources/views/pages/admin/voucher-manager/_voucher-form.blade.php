@php
    $normalizeDecimal = static function ($value): string {
        if ($value === null || $value === '') {
            return '';
        }

        return rtrim(rtrim((string) $value, '0'), '.');
    };

    $normalizeDate = static function ($value): string {
        if ($value === null || $value === '') {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return '';
        }
    };

    $isEdit = isset($voucher) && $voucher;
    $formAction = $isEdit ? route('admin.update-voucher', $voucher) : route('admin.store-voucher');

    $code = old('code', $voucher->code ?? '');
    $discountType = old('discount_type', $voucher->discount_type ?? 'percent');
    $discountValue = $normalizeDecimal(old('discount_value', $voucher->discount_value ?? ''));
    $minOrderValue = $normalizeDecimal(old('min_order_value', $voucher->min_order_value ?? 0));
    $maxDiscount = $normalizeDecimal(old('max_discount', $voucher->max_discount ?? ''));
    $category = old('category', $voucher->category ?? 'all');
    $categoryId = old('category_id', $voucher->category_id ?? '');
    $collectionId = old('collection_id', $voucher->collection_id ?? '');
    $productId = old('product_id', $voucher->product_id ?? '');
    $usageLimit = old('usage_limit', $voucher->usage_limit ?? '');

    $startDate = $normalizeDate(
        old('start_date', isset($voucher) && $voucher->start_date ? $voucher->start_date : now()->toDateString()),
    );
    $endDate = $normalizeDate(
        old(
            'end_date',
            isset($voucher) && $voucher->end_date ? $voucher->end_date : now()->addDays(30)->toDateString(),
        ),
    );

    $isActive = (bool) old('is_active', isset($voucher) ? $voucher->is_active : true);
    $discountText =
        $discountType === 'percent'
            ? 'Giảm ' .
                rtrim(
                    rtrim(number_format((float) ($discountValue !== '' ? $discountValue : 0), 2, '.', ''), '0'),
                    '.',
                ) .
                '%'
            : ($discountType === 'shipping'
                ? 'Giảm phí vận chuyển ' .
                    number_format((float) ($discountValue !== '' ? $discountValue : 0), 0, ',', '.') .
                    'đ'
                : 'Giảm ' . number_format((float) ($discountValue !== '' ? $discountValue : 0), 0, ',', '.') . 'đ');
    $minOrderText = 'Cho đơn hàng từ ' . number_format((float) $minOrderValue, 0, ',', '.') . 'đ';
    $endDateText = $endDate !== '' ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '--/--/----';

    $selectedCategory = collect($categoryOptions ?? [])->firstWhere('id', (int) $categoryId);
    $selectedCollection = collect($collectionOptions ?? [])->firstWhere('id', (int) $collectionId);
    $selectedProduct = collect($productOptions ?? [])->firstWhere('id', (int) $productId);

    $categoryKeyword = old('category_keyword', $selectedCategory->name ?? '');
    $collectionKeyword = old('collection_keyword', $selectedCollection->name ?? '');
    $productKeyword = old(
        'product_keyword',
        $selectedProduct
            ? trim(
                ($selectedProduct->product_code ? $selectedProduct->product_code . ' - ' : '') . $selectedProduct->name,
            )
            : '',
    );
@endphp

@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
        <p class="font-semibold">Vui lòng kiểm tra lại thông tin voucher.</p>
    </div>
@endif

<form action="{{ $formAction }}" method="POST" data-voucher-form>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-gray-50 shadow-sm">
                <div>
                    <h4 class="text-[11px] font-black text-[#bc9c75] uppercase mb-5">Thông tin cơ bản</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Mã giảm giá
                                (Code)</label>
                            <input type="text" name="code" value="{{ $code }}"
                                placeholder="Ví dụ: QUY2_2026" @disabled($isEdit)
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-2xl py-3.5 px-5 text-xs font-mono font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none uppercase placeholder:text-gray-300">
                            @error('code')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Loại chiết
                                khấu (Type)</label>
                            <select name="discount_type" @disabled($isEdit)
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none appearance-none cursor-pointer">
                                <option value="fixed" @selected($discountType === 'fixed')>Số tiền cố định (VNĐ)</option>
                                <option value="percent" @selected($discountType === 'percent')>Phần trăm (%)</option>
                                <option value="shipping" @selected($discountType === 'shipping')>Giảm phí vận chuyển (VNĐ)</option>
                            </select>
                            @error('discount_type')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50">
                    <h4 class="text-[11px] font-black text-[#bc9c75] uppercase mb-5">Giá trị & Hạn mức</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Mức giảm
                                (Value)</label>
                            <input type="number" name="discount_value" step="0.01" min="0"
                                max="9999999999.99" value="{{ $discountValue }}" @disabled($isEdit)
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                            @error('discount_value')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Đơn tối
                                thiểu (Min Order)</label>
                            <input type="number" name="min_order_value" step="0.01" min="0"
                                max="9999999999.99" value="{{ $minOrderValue }}" @disabled($isEdit)
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                            @error('min_order_value')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div data-max-discount-group>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Giảm tối
                                đa (Max Discount)</label>
                            <input type="number" name="max_discount" step="0.01" min="0" max="9999999999.99"
                                value="{{ $maxDiscount }}" @disabled($isEdit)
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-2xl py-3.5 px-5 text-xs font-bold focus:ring-2 focus:ring-[#bc9c75]/20 outline-none">
                            @error('max_discount')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50">
                    <h4 class="text-[11px] font-black text-[#bc9c75] uppercase mb-5">Phạm vi áp dụng</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Loại áp
                                dụng</label>
                            <select name="category" id="voucher-category" @disabled($isEdit)
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 text-xs font-semibold">
                                <option value="all" @selected($category === 'all')>Toàn bộ sản phẩm</option>
                                <option value="category" @selected($category === 'category')>Theo danh mục</option>
                                <option value="collection" @selected($category === 'collection')>Theo bộ sưu tập</option>
                                <option value="product" @selected($category === 'product')>Theo sản phẩm</option>
                            </select>
                            @error('category')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="hidden text-[11px] text-sky-500 mt-1" id="shipping-category-note">
                                Voucher giảm phí vận chuyển sẽ tự áp dụng cho toàn bộ đơn hàng.</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Số lượng
                                phát hành</label>
                            <input type="number" name="usage_limit" min="1" value="{{ $usageLimit }}"
                                placeholder="Để trống nếu không giới hạn"
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 text-xs font-semibold">
                            @error('usage_limit')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div data-id-group="category">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Danh
                                mục</label>
                            <input type="hidden" name="category_id" id="category-id-hidden"
                                value="{{ $categoryId }}">
                            <input type="text" id="category-search-input" name="category_keyword"
                                value="{{ $categoryKeyword }}" @disabled($isEdit)
                                placeholder="Gõ tên danh mục..."
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 text-xs font-semibold">
                            <datalist id="category-options-list">
                                @foreach ($categoryOptions ?? [] as $categoryOption)
                                    <option value="{{ $categoryOption->name }}" data-id="{{ $categoryOption->id }}">
                                    </option>
                                @endforeach
                            </datalist>
                            @error('category_id')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div data-id-group="collection">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Bộ sưu
                                tập</label>
                            <input type="hidden" name="collection_id" id="collection-id-hidden"
                                value="{{ $collectionId }}">
                            <input type="text" id="collection-search-input" name="collection_keyword"
                                value="{{ $collectionKeyword }}" @disabled($isEdit)
                                placeholder="Gõ tên bộ sưu tập..."
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 text-xs font-semibold">
                            <datalist id="collection-options-list">
                                @foreach ($collectionOptions ?? [] as $collectionOption)
                                    <option value="{{ $collectionOption->name }}"
                                        data-id="{{ $collectionOption->id }}"></option>
                                @endforeach
                            </datalist>
                            @error('collection_id')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div data-id-group="product">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Sản phẩm
                                (mã/tên)</label>
                            <input type="hidden" name="product_id" id="product-id-hidden"
                                value="{{ $productId }}">
                            <input type="text" id="product-search-input" name="product_keyword"
                                value="{{ $productKeyword }}" @disabled($isEdit)
                                placeholder="Gõ mã hoặc tên sản phẩm..."
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 text-xs font-semibold">
                            <datalist id="product-options-list">
                                @foreach ($productOptions ?? [] as $productOption)
                                    <option
                                        value="{{ trim(($productOption->product_code ? $productOption->product_code . ' - ' : '') . $productOption->name) }}"
                                        data-id="{{ $productOption->id }}">
                                    </option>
                                @endforeach
                            </datalist>
                            @error('product_id')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50">
                    <h4 class="text-[11px] font-black text-[#bc9c75] uppercase mb-5">Thời gian</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Ngày bắt
                                đầu</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-xs font-semibold">
                            @error('start_date')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Ngày kết
                                thúc</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="w-full mt-2 bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-xs font-semibold">
                            @error('end_date')
                                <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-gray-50 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">Trạng thái (Active)</h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                            @checked($isActive) @disabled($isEdit)>
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#bc9c75]">
                        </div>
                    </label>
                </div>
                <p class="text-[10px] text-gray-400 font-medium leading-relaxed">Khi kích hoạt, khách hàng có thể áp
                    dụng mã này ngay lập tức nếu thỏa mãn điều kiện.</p>
            </div>

            <div
                class="bg-[#bc9c75] p-6 rounded-4xl shadow-xl shadow-[#bc9c75]/20 relative overflow-hidden text-white min-h-48 flex flex-col justify-between">
                <i class="fa-solid fa-ticket absolute -right-6 -top-6 text-white/10 text-9xl rotate-12"></i>

                <div class="relative z-10">
                    <span
                        class="text-[9px] font-black uppercase tracking-[0.2em] px-2 py-1 bg-white/20 rounded">Preview
                        Voucher</span>
                    <h4 class="text-2xl font-black mt-4 uppercase tracking-tighter" id="preview-value">
                        {{ $discountText }}</h4>
                    <p class="text-[10px] opacity-80 mt-1 font-medium" id="preview-min-order">{{ $minOrderText }}</p>
                </div>

                <div class="relative z-10 pt-4 border-t border-white/20 flex justify-between items-end gap-3">
                    <div>
                        <p class="text-[10px] font-bold opacity-60 uppercase">Mã: <span id="preview-code"
                                class="text-white ml-1">{{ $code !== '' ? $code : 'CHƯA NHẬP' }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] opacity-60 uppercase font-bold text-white/50 italic">Hết hạn: <span
                                id="preview-end-date">{{ $endDateText }}</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50/50 border border-blue-100/50 p-6 rounded-3xl">
                <div class="flex items-center gap-3 mb-3 text-blue-500">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                    <span class="text-[11px] font-black uppercase tracking-wider">Lưu ý</span>
                </div>
                <ul class="text-[10px] text-blue-400 space-y-2 font-medium">
                    <li class="flex gap-2">• <span class="flex-1">Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt
                            đầu.</span></li>
                </ul>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.voucher-manager') }}"
                    class="px-6 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 transition">Hủy
                    bỏ</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#bc9c75] text-white rounded-xl text-xs font-bold shadow-md shadow-[#bc9c75]/20 hover:opacity-90 transition">
                    {{ $isEdit ? 'Cập nhật Voucher' : 'Tạo Voucher' }}
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
    <script src="/extra-assets/js/voucher-form.js"></script>
@endpush
