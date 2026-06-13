@extends('layouts.user-layout')

@section('title', 'Giỏ hàng')

@section('content')
    @php
        $authUser = auth()->user();
        $isLoggedIn = !is_null($authUser);
        $missingPhone = $isLoggedIn && trim((string) ($authUser->phone_number ?? '')) === '';
        $missingAddress = $isLoggedIn && trim((string) ($authUser->address ?? '')) === '';
        $profileIncomplete = $missingPhone || $missingAddress;

        $selectedCount = count($selectedCartItemIds ?? []);
        $allSelected = count($cartItems) > 0 && $selectedCount === count($cartItems);
    @endphp

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-6xl mx-auto px-4">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Giỏ hàng</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Bạn có <span class="font-semibold">{{ count($cartItems) }}</span> sản phẩm trong giỏ hàng
                </p>
            </div>

            @if (count($cartItems) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <form id="selectionForm" method="GET" action="{{ route('user.cart') }}">
                            <div
                                class="mb-3 flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3">
                                <label class="inline-flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" id="selectAll"
                                        class="rounded border-gray-300 text-[#bc9c75] focus:ring-[#bc9c75]"
                                        @if ($allSelected) checked @endif>
                                    <span class="text-sm font-medium text-gray-700">Chọn tất cả sản phẩm</span>
                                </label>
                                <p class="text-sm text-gray-600">
                                    Đã chọn <span id="selectedCount"
                                        class="font-semibold text-gray-900">{{ $selectedCount }}</span>
                                    / {{ count($cartItems) }} sản phẩm
                                </p>
                            </div>

                            <div class="space-y-4">
                                @foreach ($cartItems as $item)
                                    <div
                                        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition flex gap-4 p-4">
                                        <div class="shrink-0 pt-1">
                                            <input type="checkbox" name="selected_cart_ids[]" value="{{ $item['id'] }}"
                                                class="item-checkbox rounded border-gray-300 text-[#bc9c75] focus:ring-[#bc9c75]"
                                                @if (in_array($item['id'], $selectedCartItemIds)) checked @endif
                                                onchange="document.getElementById('selectionForm').submit();">
                                        </div>

                                        <div class="shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                                            <img src="{{ $item['product_image'] }}" alt="{{ $item['product_name'] }}"
                                                class="w-full h-full object-cover">
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 truncate">{{ $item['product_name'] }}
                                            </h3>
                                            <p class="text-sm text-gray-600">SKU: {{ $item['sku'] }}</p>
                                            <p class="text-sm text-gray-500">
                                                @if ($item['size'] !== '')
                                                    Size {{ $item['size'] }}
                                                @endif
                                                @if ($item['color'] !== '')
                                                    - Màu {{ $item['color'] }}
                                                @endif
                                            </p>
                                            @php
                                                $hasSalePrice =
                                                    !is_null($item['sale_price']) &&
                                                    $item['sale_price'] < $item['base_price'];
                                            @endphp

                                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                                <p
                                                    class="font-semibold {{ $hasSalePrice ? 'text-red-600' : 'text-[#bc9c75]' }}">
                                                    {{ number_format($item['unit_price'], 0, ',', '.') }}₫
                                                </p>
                                                @if ($hasSalePrice)
                                                    <p class="text-xs text-gray-400 line-through">
                                                        {{ number_format($item['base_price'], 0, ',', '.') }}₫
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="shrink-0 flex items-center gap-2">


                                            <form method="POST"
                                                action="{{ route('user.cart.update', ['cart' => $item['id']]) }}"
                                                class="inline-flex items-center">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity"
                                                    value="{{ max(1, $item['quantity'] - 1) }}">
                                                <button type="submit"
                                                    class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition"
                                                    aria-label="Giảm">
                                                    <i class="ri-subtract-line text-sm"></i>
                                                </button>
                                            </form>

                                            <form method="POST"
                                                action="{{ route('user.cart.update', ['cart' => $item['id']]) }}"
                                                class="mx-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                                    min="1"
                                                    @if ($item['max_stock'] > 0) max="{{ $item['max_stock'] }}" @endif
                                                    class="w-14 h-8 text-center border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#bc9c75] text-sm"
                                                    onchange="this.form.submit()">
                                            </form>

                                            <form method="POST"
                                                action="{{ route('user.cart.update', ['cart' => $item['id']]) }}"
                                                class="inline-flex items-center">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity"
                                                    value="{{ min($item['max_stock'] > 0 ? $item['max_stock'] : $item['quantity'] + 1, $item['quantity'] + 1) }}">
                                                <button type="submit" @if ($item['max_stock'] > 0 && $item['quantity'] >= $item['max_stock']) disabled @endif
                                                    class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition disabled:opacity-40 disabled:cursor-not-allowed"
                                                    aria-label="Tăng">
                                                    <i class="ri-add-line text-sm"></i>
                                                </button>
                                            </form>

                                            <form method="POST"
                                                action="{{ route('user.cart.remove', ['cart' => $item['id']]) }}"
                                                class="inline-block ml-3">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 text-sm font-semibold mt-2 hover:text-red-700 transition">Xóa</button>
                                            </form>
                                        </div>

                                        <div class="shrink-0 text-right">
                                            <p class="font-bold text-gray-900">
                                                {{ number_format($item['line_total'], 0, ',', '.') }}₫
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4 space-y-6">
                            <div class="border-b border-gray-200 pb-4">
                                @if ($selectedCount > 0)
                                    <div class="flex justify-between mb-3">
                                        <span class="text-gray-600">Tạm tính</span>
                                        <span id="subtotal"
                                            class="font-semibold">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                                    </div>
                                    <div class="flex justify-between mb-3">
                                        <span class="text-gray-600">Giao hàng</span>
                                        <span id="shipping"
                                            class="font-semibold">{{ number_format($shipping, 0, ',', '.') }}₫</span>
                                    </div>
                                    <div id="membershipDiscountRow" class="{{ $membershipDiscount > 0 ? '' : 'hidden' }}">
                                        <div class="flex justify-between mb-3">
                                            <span class="text-gray-600">Giảm giá hạng thành viên</span>
                                            <span id="membershipDiscount"
                                                class="font-semibold text-green-600">-{{ number_format($membershipDiscount, 0, ',', '.') }}₫</span>
                                        </div>
                                    </div>
                                    <div id="discountRow" class="{{ $discount > 0 ? '' : 'hidden' }}">
                                        <div class="flex justify-between mb-3">
                                            <span class="text-gray-600">Giảm giá voucher</span>
                                            <span id="discount"
                                                class="font-semibold text-green-600">-{{ number_format($discount, 0, ',', '.') }}₫</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-600">Chọn sản phẩm để xem tạm tính và voucher.</div>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Mã khuyến mãi</label>

                                @if ($selectedCount > 0)
                                    @if (auth()->check() && count($availableVouchers) > 0)
                                        <form id="voucherForm" method="GET" action="{{ route('user.cart') }}">
                                            @foreach ($selectedCartItemIds as $scid)
                                                <input type="hidden" name="selected_cart_ids[]"
                                                    value="{{ $scid }}">
                                            @endforeach
                                            <select id="voucherSelect" name="voucher_code"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#bc9c75]">
                                                <option value="">Không dùng voucher</option>
                                                @foreach ($availableVouchers as $voucher)
                                                    <option value="{{ $voucher['code'] }}"
                                                        @if (strtoupper($voucher['code']) === strtoupper($selectedVoucherCode)) selected @endif>
                                                        {{ $voucher['display'] }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        <p class="mt-2 text-xs text-gray-500">Chỉ voucher đã lưu trong ví mới dùng được.
                                        </p>
                                    @else
                                        @guest
                                            <div
                                                class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-2 text-xs text-gray-500">
                                                Đăng nhập để lưu và sử dụng voucher.
                                            </div>
                                        @endguest
                                    @endif
                                @else
                                    <div
                                        class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-2 text-xs text-gray-500">
                                        Chọn sản phẩm để sử dụng voucher và xem tạm tính.</div>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-3">Phương thức thanh
                                    toán</label>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="payment_method" value="cod" form="checkoutForm"
                                            @if (($paymentMethod ?? 'cod') === 'cod') checked @endif
                                            class="text-[#bc9c75] focus:ring-[#bc9c75]">
                                        <span class="text-sm text-gray-700">Thanh toán khi nhận hàng (COD)</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="payment_method" value="vnpay" form="checkoutForm"
                                            @if (($paymentMethod ?? '') === 'vnpay') checked @endif
                                            class="text-[#bc9c75] focus:ring-[#bc9c75]">
                                        <span class="text-sm text-gray-700">VNPay (ATM/QR/Internet Banking)</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="payment_method" value="stripe" form="checkoutForm"
                                            @if (($paymentMethod ?? '') === 'stripe') checked @endif
                                            class="text-[#bc9c75] focus:ring-[#bc9c75]">
                                        <span class="text-sm text-gray-700">Thẻ Visa/Mastercard (Stripe)</span>
                                    </label>
                                </div>
                            </div>

                            <div class="bg-linear-to-r from-[#bc9c75]/10 to-[#bc9c75]/5 rounded-lg p-4">
                                <div class="flex justify-between items-baseline">
                                    <span class="text-gray-900 font-semibold">Tổng cộng (<span
                                            id="selectedCountInTotal">{{ $selectedCount }}</span> SP chọn)</span>
                                    <span id="totalAmount"
                                        class="text-2xl font-bold text-[#bc9c75]">{{ number_format($total, 0, ',', '.') }}₫</span>
                                </div>
                            </div>

                            <form id="checkoutForm" action="{{ route('user.checkout') }}" method="POST"
                                class="space-y-3">
                                @csrf
                                <input type="hidden" name="voucher_code" value="{{ $selectedVoucherCode }}">
                                @foreach ($selectedCartItemIds as $selectedCartId)
                                    <input type="hidden" name="selected_cart_ids[]" value="{{ $selectedCartId }}">
                                @endforeach

                                @if (!auth()->check())
                                    <div class="space-y-3 rounded-lg border border-[#eadfce] bg-[#fcfaf7] p-3">
                                        <p class="text-sm font-semibold text-gray-900">Thông tin nhận hàng</p>
                                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                            placeholder="Họ và tên người nhận"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#bc9c75]">
                                        @error('customer_name')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror

                                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                            placeholder="Số điện thoại người nhận"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#bc9c75]">
                                        @error('customer_phone')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror

                                        <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                            placeholder="Email (không bắt buộc)"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#bc9c75]">
                                        @error('customer_email')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror

                                        <textarea name="shipping_address" rows="2" placeholder="Địa chỉ nhận hàng"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#bc9c75]">{{ old('shipping_address') }}</textarea>
                                        @error('shipping_address')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @else
                                    @if ($profileIncomplete)
                                        <div
                                            class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-800">
                                            <p class="font-semibold">Bạn chưa cập nhật đủ thông tin nhận hàng.</p>
                                            <p class="mt-1">Vui lòng bổ sung số điện thoại và địa chỉ trong hồ sơ để tiếp
                                                tục đặt hàng.</p>
                                            <a href="{{ route('user.profile') }}"
                                                class="mt-2 inline-flex items-center gap-1 font-semibold text-amber-900 underline">Cập
                                                nhật hồ sơ ngay</a>
                                        </div>
                                    @else
                                        <div
                                            class="rounded-lg border border-[#dce8dd] bg-[#f3fbf4] p-3 text-sm text-gray-700">
                                            Đơn hàng sẽ dùng thông tin tài khoản của bạn. Bạn có thể cập nhật tại trang hồ
                                            sơ nếu cần.</div>
                                    @endif
                                @endif

                                <button id="checkoutButton" type="submit" @disabled($selectedCount === 0 || ($isLoggedIn && $profileIncomplete))
                                    class="w-full bg-[#bc9c75] text-white py-3 rounded-lg font-semibold hover:bg-[#a88966] transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="ri-shopping-bag-line mr-2"></i>
                                    Đặt hàng
                                </button>

                                <a href="{{ route('dashboard') }}"
                                    class="block w-full bg-gray-200 text-gray-800 py-3 px-4 rounded-lg font-semibold hover:bg-gray-300 transition text-center">Tiếp
                                    tục mua sắm</a>
                            </form>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700 space-y-2">
                                <p><i class="ri-information-line"></i> Đơn từ 499.000đ được miễn phí vận chuyển.</p>
                                <p><i class="ri-truck-line"></i> Phí giao hàng hiện tại:
                                    {{ number_format($shipping, 0, ',', '.') }}₫</p>
                                <p><i class="ri-check-line"></i> Đổi trả trong 7 ngày nếu sản phẩm lỗi</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                    <i class="ri-shopping-cart-line text-6xl text-gray-300 mb-4 block"></i>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Giỏ hàng trống</h2>
                    <p class="text-gray-600 mb-6">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                    <a href="{{ route('user.product') }}"
                        class="inline-block bg-[#bc9c75] text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#a88966] transition">
                        <i class="ri-shopping-bag-line mr-2"></i> Tiếp tục mua sắm
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            const profileIncomplete = @json($profileIncomplete);
            // Toggle select all and submit form
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            async function fetchTotals(selectedIds = []) {
                try {
                    const res = await fetch('{{ route('user.cart.totals') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            selected_cart_ids: selectedIds
                        })
                    });

                    if (!res.ok) return;
                    const data = await res.json();

                    document.getElementById('subtotal').textContent = new Intl.NumberFormat('vi-VN').format(data.subtotal) +
                        '₫';
                    document.getElementById('shipping').textContent = new Intl.NumberFormat('vi-VN').format(data.shipping) +
                        '₫';
                    if (data.membershipDiscount > 0) {
                        document.getElementById('membershipDiscount').textContent = '-' + new Intl.NumberFormat('vi-VN')
                            .format(data
                                .membershipDiscount) + '₫';
                        document.getElementById('membershipDiscountRow').classList.remove('hidden');
                    } else {
                        document.getElementById('membershipDiscountRow').classList.add('hidden');
                    }
                    if (data.discount > 0) {
                        document.getElementById('discount').textContent = '-' + new Intl.NumberFormat('vi-VN').format(data
                            .discount) + '₫';
                        document.getElementById('discountRow').classList.remove('hidden');
                    } else {
                        document.getElementById('discountRow').classList.add('hidden');
                    }
                    document.getElementById('totalAmount').textContent = new Intl.NumberFormat('vi-VN').format(data.total) +
                        '₫';

                    // update selected count
                    const countSpan = document.getElementById('selectedCount');
                    const countSpanInTotal = document.getElementById('selectedCountInTotal');
                    if (countSpan) countSpan.textContent = data.selectedCount;
                    if (countSpanInTotal) countSpanInTotal.textContent = data.selectedCount;

                    // enable/disable checkout
                    const checkoutBtn = document.getElementById('checkoutButton');
                    if (checkoutBtn) {
                        const shouldDisable = data.selectedCount === 0 || (profileIncomplete &&
                            {{ json_encode($isLoggedIn) }});
                        checkoutBtn.disabled = shouldDisable;
                    }
                    // update voucher options and sync hidden input
                    const voucherSelect = document.getElementById('voucherSelect');
                    if (voucherSelect) {
                        // preserve current value
                        const cur = voucherSelect.value;
                        voucherSelect.innerHTML = '<option value="">Không dùng voucher</option>';
                        (data.availableVouchers || []).forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.code;
                            opt.textContent = v.display;
                            voucherSelect.appendChild(opt);
                        });
                        voucherSelect.value = cur;

                        // sync checkout hidden input with current voucher selection
                        const hiddenVoucherInput = document.querySelector('input[name="voucher_code"]');
                        if (hiddenVoucherInput) hiddenVoucherInput.value = voucherSelect.value || '';
                    }
                } catch (e) {
                    console.error(e);
                }
            }

            document.getElementById('selectAll')?.addEventListener('change', function(e) {
                const checked = e.target.checked;
                document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = checked);
                const ids = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(i => i.value);
                fetchTotals(ids);
            });

            document.querySelectorAll('.item-checkbox').forEach(cb => cb.addEventListener('change', function() {
                const ids = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(i => i.value);
                fetchTotals(ids);
            }));

            document.getElementById('voucherSelect')?.addEventListener('change', function() {
                const ids = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(i => i.value);
                const select = this;

                // sync checkout hidden input immediately so form submission uses selected voucher
                const hiddenVoucherInput = document.querySelector('input[name="voucher_code"]');
                if (hiddenVoucherInput) hiddenVoucherInput.value = select.value || '';

                (async () => {
                    try {
                        const res = await fetch('{{ route('user.cart.totals') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                selected_cart_ids: ids,
                                voucher_code: select.value
                            })
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        document.getElementById('subtotal').textContent = new Intl.NumberFormat('vi-VN').format(
                            data.subtotal) + '₫';
                        document.getElementById('shipping').textContent = new Intl.NumberFormat('vi-VN').format(
                            data.shipping) + '₫';
                        if (data.membershipDiscount > 0) {
                            document.getElementById('membershipDiscount').textContent = '-' + new Intl
                                .NumberFormat(
                                    'vi-VN').format(data.membershipDiscount) + '₫';
                            document.getElementById('membershipDiscountRow').classList.remove('hidden');
                        } else {
                            document.getElementById('membershipDiscountRow').classList.add('hidden');
                        }
                        if (data.discount > 0) {
                            document.getElementById('discount').textContent = '-' + new Intl.NumberFormat(
                                'vi-VN').format(data.discount) + '₫';
                            document.getElementById('discountRow').classList.remove('hidden');
                        } else {
                            document.getElementById('discountRow').classList.add('hidden');
                        }
                        document.getElementById('totalAmount').textContent = new Intl.NumberFormat('vi-VN')
                            .format(data.total) + '₫';
                        // update selected count in header
                        const countSpan2 = document.getElementById('selectedCount');
                        const countSpanInTotal2 = document.getElementById('selectedCountInTotal');
                        if (countSpan2) countSpan2.textContent = ids.length;
                        if (countSpanInTotal2) countSpanInTotal2.textContent = ids.length;
                        const checkoutBtn2 = document.getElementById('checkoutButton');
                        if (checkoutBtn2) {
                            const shouldDisable = ids.length === 0 || (profileIncomplete &&
                                {{ json_encode($isLoggedIn) }});
                            checkoutBtn2.disabled = shouldDisable;
                        }
                    } catch (e) {
                        console.error(e)
                    }
                })();
            });
        </script>
    @endpush

@endsection
