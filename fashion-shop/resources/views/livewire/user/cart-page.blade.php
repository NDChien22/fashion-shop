<div class="min-h-screen bg-gray-50 py-8">
    @php
        $authUser = auth()->user();
        $isLoggedIn = !is_null($authUser);
        $missingPhone = $isLoggedIn && trim((string) ($authUser->phone_number ?? '')) === '';
        $missingAddress = $isLoggedIn && trim((string) ($authUser->address ?? '')) === '';
        $profileIncomplete = $missingPhone || $missingAddress;
    @endphp

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
                    <div
                        class="mb-3 flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3">
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:click="toggleSelectAll" @checked($this->allSelected)
                                class="rounded border-gray-300 text-[#bc9c75] focus:ring-[#bc9c75]">
                            <span class="text-sm font-medium text-gray-700">Chọn tất cả sản phẩm</span>
                        </label>
                        <p class="text-sm text-gray-600">
                            Đã chọn <span class="font-semibold text-gray-900">{{ $this->selectedCount }}</span>
                            / {{ count($cartItems) }} sản phẩm
                        </p>
                    </div>

                    <div class="space-y-4">
                        @foreach ($cartItems as $item)
                            <div
                                class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition flex gap-4 p-4">
                                <div class="shrink-0 pt-1">
                                    <input type="checkbox" value="{{ $item['id'] }}"
                                        wire:model.live="selectedCartItemIds"
                                        class="rounded border-gray-300 text-[#bc9c75] focus:ring-[#bc9c75]">
                                </div>

                                <div class="shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                                    <img src="{{ $item['product_image'] }}" alt="{{ $item['product_name'] }}"
                                        class="w-full h-full object-cover">
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $item['product_name'] }}</h3>
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
                                            !is_null($item['sale_price']) && $item['sale_price'] < $item['base_price'];
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
                                    <button
                                        wire:click="updateQuantity({{ $item['id'] }}, {{ max(1, $item['quantity'] - 1) }})"
                                        class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition">
                                        <i class="ri-subtract-line text-sm"></i>
                                    </button>

                                    <input type="number" value="{{ $item['quantity'] }}"
                                        wire:change="updateQuantity({{ $item['id'] }}, $event.target.value)"
                                        min="1"
                                        @if ($item['max_stock'] > 0) max="{{ $item['max_stock'] }}" @endif
                                        class="w-14 h-8 text-center border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#bc9c75] text-sm">

                                    <button
                                        wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                        @disabled($item['max_stock'] > 0 && $item['quantity'] >= $item['max_stock'])
                                        class="w-8 h-8 rounded border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i class="ri-add-line text-sm"></i>
                                    </button>
                                </div>

                                <div class="shrink-0 text-right">
                                    <p class="font-bold text-gray-900">
                                        {{ number_format($item['line_total'], 0, ',', '.') }}₫
                                    </p>
                                    <button wire:click="removeItem({{ $item['id'] }})"
                                        class="text-red-600 text-sm font-semibold mt-2 hover:text-red-700 transition">
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4 space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between mb-3">
                                <span class="text-gray-600">Tạm tính</span>
                                <span class="font-semibold">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="flex justify-between mb-3">
                                <span class="text-gray-600">Giao hàng</span>
                                <span class="font-semibold">{{ number_format($shipping, 0, ',', '.') }}₫</span>
                            </div>
                            @if ($discount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Giảm giá</span>
                                    <span
                                        class="font-semibold text-green-600">-{{ number_format($discount, 0, ',', '.') }}₫</span>
                                </div>
                            @endif
                        </div>

                        @if (auth()->check() && count($availableVouchers) > 0)
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Mã khuyến mãi</label>
                                <select wire:model.live="selectedVoucherCode"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#bc9c75]">
                                    <option value="">Không dùng voucher</option>
                                    @foreach ($availableVouchers as $voucher)
                                        <option value="{{ $voucher['code'] }}">{{ $voucher['display'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif (!auth()->check())
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Mã khuyến mãi</label>
                                <input type="text" wire:model.live.debounce.300ms="selectedVoucherCode"
                                    placeholder="Nhập mã voucher (nếu có)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#bc9c75]">
                                <p class="mt-2 text-xs text-gray-500">Hệ thống sẽ tự kiểm tra mã hợp lệ trước khi thanh
                                    toán.</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-3">Phương thức thanh toán</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="payment_method" value="cod"
                                        wire:model="paymentMethod" class="text-[#bc9c75] focus:ring-[#bc9c75]">
                                    <span class="text-sm text-gray-700">Thanh toán khi nhận hàng (COD)</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="payment_method" value="vnpay"
                                        wire:model="paymentMethod" class="text-[#bc9c75] focus:ring-[#bc9c75]">
                                    <span class="text-sm text-gray-700">VNPay (ATM/QR/Internet Banking)</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="payment_method" value="stripe"
                                        wire:model="paymentMethod" class="text-[#bc9c75] focus:ring-[#bc9c75]">
                                    <span class="text-sm text-gray-700">Thẻ Visa/Mastercard (Stripe)</span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-linear-to-r from-[#bc9c75]/10 to-[#bc9c75]/5 rounded-lg p-4">
                            <div class="flex justify-between items-baseline">
                                <span class="text-gray-900 font-semibold">Tổng cộng ({{ $this->selectedCount }} SP
                                    chọn)</span>
                                <span
                                    class="text-2xl font-bold text-[#bc9c75]">{{ number_format($total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>

                        <form action="{{ route('user.checkout') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="voucher_code" value="{{ $selectedVoucherCode }}">
                            <input type="hidden" name="payment_method" wire:model="paymentMethod">
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
                                        <a wire:navigate href="{{ route('user.profile') }}"
                                            class="mt-2 inline-flex items-center gap-1 font-semibold text-amber-900 underline">
                                            Cập nhật hồ sơ ngay
                                        </a>
                                    </div>
                                @else
                                    <div
                                        class="rounded-lg border border-[#dce8dd] bg-[#f3fbf4] p-3 text-sm text-gray-700">
                                        Đơn hàng sẽ dùng thông tin tài khoản của bạn. Bạn có thể cập nhật tại trang hồ
                                        sơ
                                        nếu cần.
                                    </div>
                                @endif
                            @endif

                            @error('payment_method')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            @error('voucher_code')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            @error('selected_cart_ids')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            <button type="submit" @disabled($this->selectedCount === 0 || ($isLoggedIn && $profileIncomplete))
                                class="w-full bg-[#bc9c75] text-white py-3 rounded-lg font-semibold hover:bg-[#a88966] transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="ri-shopping-bag-line mr-2"></i>
                                Thanh toán sản phẩm đã chọn
                            </button>

                            <a wire:navigate href="{{ route('dashboard') }}"
                                class="block w-full bg-gray-200 text-gray-800 py-3 px-4 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                                Tiếp tục mua sắm
                            </a>
                        </form>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700 space-y-2">
                            <p><i class="ri-information-line"></i>
                                Đơn từ 499.000đ được miễn phí vận chuyển.</p>
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
                <a wire:navigate href="{{ route('user.product') }}"
                    class="inline-block bg-[#bc9c75] text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#a88966] transition">
                    <i class="ri-shopping-bag-line mr-2"></i>
                    Tiếp tục mua sắm
                </a>
            </div>
        @endif
    </div>
</div>
