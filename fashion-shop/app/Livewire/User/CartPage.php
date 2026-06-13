<?php

namespace App\Livewire\User;

use App\Models\Cart;
use App\Models\CustomerMembershipLevel;
use App\Models\Products;
use App\Models\UserVoucher;
use App\Models\Voucher;
use App\Services\VoucherService;
use App\Support\FlashSalePricing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class CartPage extends Component
{
    private const SHIPPING_FEE = 30000;

    private const FREE_SHIPPING_THRESHOLD = 499000;

    public array $cartItems = [];

    public array $selectedCartItemIds = [];

    public array $availableVouchers = [];

    public string $selectedVoucherCode = '';

    public string $paymentMethod = 'cod';

    public float $subtotal = 0.0;

    public float $shipping = 0.0;

    public float $discount = 0.0;

    public float $membershipDiscount = 0.0;

    public float $total = 0.0;

    private bool $selectionInitialized = false;

    public function mount(): void
    {
        if (Auth::check()) {
            Cart::mergeSessionToUser(session()->getId(), (int) Auth::id());
        }

        $this->selectedVoucherCode = trim((string) old('voucher_code', ''));

        $this->refreshState();
    }

    public function updatedSelectedVoucherCode(): void
    {
        $this->calculateTotals();
    }

    public function updatedSelectedCartItemIds(): void
    {
        $availableIds = collect($this->cartItems)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->selectedCartItemIds = collect($this->selectedCartItemIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $availableIds, true))
            ->unique()
            ->values()
            ->all();

        $this->calculateTotals();
    }

    public function toggleSelectAll(): void
    {
        $allIds = collect($this->cartItems)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (count($allIds) > 0 && count($this->selectedCartItemIds) === count($allIds)) {
            $this->selectedCartItemIds = [];
        } else {
            $this->selectedCartItemIds = $allIds;
        }

        $this->calculateTotals();
    }

    public function updateQuantity(int $cartId, int|string $quantity): void
    {
        $item = Cart::query()->with('productSku')->find($cartId);

        if (! $item || ! $this->belongsToActor($item)) {
            return;
        }

        $nextQuantity = max(1, (int) $quantity);
        $stock = (int) ($item->productSku?->stock ?? 0);

        if ($stock > 0 && $nextQuantity > $stock) {
            $nextQuantity = $stock;
            session()->flash('error', 'Số lượng vượt quá tồn kho hiện tại.');
        }

        $item->update([
            'quantity' => $nextQuantity,
        ]);
        $this->refreshState();
        $this->refreshState();
    }

    public function removeItem(int $cartId): void
    {
        $item = Cart::query()->find($cartId);

        if (! $item || ! $this->belongsToActor($item)) {
            return;
        }

        $item->delete();
        $this->refreshState();
    }

    public function render()
    {
        return view('livewire.user.cart-page');
    }

    public function getSelectedCountProperty(): int
    {
        return count($this->selectedCartItemIds);
    }

    public function getAllSelectedProperty(): bool
    {
        $total = count($this->cartItems);

        return $total > 0 && count($this->selectedCartItemIds) === $total;
    }

    private function refreshState(): void
    {
        $flashSales = FlashSalePricing::activeSales();

        $items = $this->cartQuery()
            ->with(['productSku.product'])
            ->orderByDesc('id')
            ->get();

        $this->cartItems = $items->map(function (Cart $item) use ($flashSales): array {
            $product = $item->productSku?->product;

            if ($product instanceof Products) {
                $product = FlashSalePricing::applyProduct($product, $flashSales);
            }

            $imageUrl = $this->resolveProductImageUrl($product);

            $quantity = (int) $item->quantity;
            $basePrice = $product instanceof Products ? (float) ($product->base_price ?? 0) : 0.0;
            $salePrice = $product instanceof Products ? FlashSalePricing::displayPrice($product) : 0.0;
            $unitPrice = $salePrice > 0 ? $salePrice : $basePrice;

            return [
                'id' => (int) $item->id,
                'product_id' => (int) ($product?->id ?? 0),
                'category_id' => (int) ($product?->category_id ?? 0),
                'collection_id' => (int) ($product?->collection_id ?? 0),
                'product_name' => (string) ($product?->name ?? 'Sản phẩm không tồn tại'),
                'product_image' => $imageUrl,
                'sku' => (string) ($item->productSku?->sku ?? '-'),
                'size' => (string) ($item->productSku?->size ?? ''),
                'color' => (string) ($item->productSku?->color ?? ''),
                'quantity' => $quantity,
                'max_stock' => (int) ($item->productSku?->stock ?? 0),
                'base_price' => $basePrice,
                'sale_price' => $salePrice > 0 && $salePrice < $basePrice ? $salePrice : null,
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * $quantity, 2),
            ];
        })->toArray();

        $allIds = collect($this->cartItems)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        if (! $this->selectionInitialized) {
            // Do not auto-select all items on initial load — let user choose which items to pay for.
            $this->selectedCartItemIds = [];
            $this->selectionInitialized = true;
        } else {
            $this->selectedCartItemIds = collect($this->selectedCartItemIds)
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $allIds->contains($id))
                ->unique()
                ->values()
                ->all();
        }

        $this->loadVouchers();

        if ($this->selectedVoucherCode !== '' && ! $this->isSelectedVoucherAvailable()) {
            $this->selectedVoucherCode = '';
        }

        $this->calculateTotals();
    }

    private function loadVouchers(): void
    {
        if (! Auth::check()) {
            $this->availableVouchers = [];

            return;
        }

        $voucherService = new VoucherService;
        $userId = (int) Auth::id();

        // Xóa voucher hết hạn và đã dùng khỏi ví
        $voucherService->cleanupExpiredAndUsedVouchers($userId);

        // Lấy danh sách voucher khả dụng
        $walletVouchers = $voucherService->getAvailableVouchersForUser($userId);

        $this->availableVouchers = $walletVouchers
            ->map(function (UserVoucher $userVoucher): ?array {
                $voucher = $userVoucher->voucher;
                if (! $voucher) {
                    return null;
                }

                return [
                    'id' => (int) $voucher->id,
                    'code' => (string) $voucher->code,
                    'discount_type' => (string) $voucher->discount_type,
                    'discount_value' => (float) $voucher->discount_value,
                    'min_order_value' => (float) ($voucher->min_order_value ?? 0),
                    'max_discount' => is_null($voucher->max_discount) ? null : (float) $voucher->max_discount,
                    'category' => (string) ($voucher->category ?? 'all'),
                    'product_id' => is_null($voucher->product_id) ? null : (int) $voucher->product_id,
                    'category_id' => is_null($voucher->category_id) ? null : (int) $voucher->category_id,
                    'collection_id' => is_null($voucher->collection_id) ? null : (int) $voucher->collection_id,
                    'display' => $this->formatVoucherLabel($voucher),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function calculateTotals(): void
    {
        $selectedItems = $this->selectedItems();

        $this->subtotal = round((float) $selectedItems->sum('line_total'), 2);
        $this->shipping = $selectedItems->isEmpty()
            ? 0.0
            : ($this->subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::SHIPPING_FEE);
        $this->discount = 0.0;
        $this->membershipDiscount = 0.0;

        // Calculate membership discount
        if (Auth::check()) {
            $customerMembership = CustomerMembershipLevel::query()
                ->where('user_id', (int) Auth::id())
                ->with('membershipLevel')
                ->first();

            if ($customerMembership?->membershipLevel?->discount_rate > 0) {
                $discountRate = (float) $customerMembership->membershipLevel->discount_rate;
                $this->membershipDiscount = round(($this->subtotal * $discountRate) / 100, 2);
            }
        }

        if ($this->selectedVoucherCode !== '') {
            $voucher = $this->selectedVoucher();
            if ($voucher) {
                $this->discount = $this->calculateVoucherDiscount($voucher);
            }
        }

        $gross = round($this->subtotal + $this->shipping, 2);
        $totalDiscount = $this->discount + $this->membershipDiscount;
        $totalDiscount = min($totalDiscount, $gross);
        $this->total = round(max($gross - $totalDiscount, 0), 2);
    }

    private function calculateVoucherDiscount(array $voucher): float
    {
        if ($this->subtotal < (float) ($voucher['min_order_value'] ?? 0)) {
            return 0.0;
        }

        if (($voucher['discount_type'] ?? '') === 'shipping') {
            return min((float) ($voucher['discount_value'] ?? 0), $this->shipping);
        }

        $eligibleSubtotal = $this->eligibleSubtotal($voucher);
        if ($eligibleSubtotal <= 0) {
            return 0.0;
        }

        $discountValue = (float) ($voucher['discount_value'] ?? 0);
        $discount = ($voucher['discount_type'] ?? '') === 'percent'
            ? ($eligibleSubtotal * $discountValue) / 100
            : $discountValue;

        if (! is_null($voucher['max_discount'])) {
            $discount = min($discount, (float) $voucher['max_discount']);
        }

        return round(min($discount, $eligibleSubtotal), 2);
    }

    private function eligibleSubtotal(array $voucher): float
    {
        $selectedItems = $this->selectedItems();

        if (($voucher['category'] ?? 'all') === 'all') {
            return (float) $selectedItems->sum('line_total');
        }

        return (float) $selectedItems->sum(function (array $item) use ($voucher): float {
            $matched = match ($voucher['category']) {
                'product' => (int) ($voucher['product_id'] ?? 0) === (int) ($item['product_id'] ?? 0),
                'category' => (int) ($voucher['category_id'] ?? 0) === (int) ($item['category_id'] ?? 0),
                'collection' => (int) ($voucher['collection_id'] ?? 0) === (int) ($item['collection_id'] ?? 0),
                default => false,
            };

            return $matched ? (float) ($item['line_total'] ?? 0) : 0.0;
        });
    }

    private function selectedVoucher(): ?array
    {
        if ($this->selectedVoucherCode === '') {
            return null;
        }

        $selectedCode = strtoupper(trim($this->selectedVoucherCode));

        foreach ($this->availableVouchers as $voucher) {
            if (strtoupper((string) ($voucher['code'] ?? '')) === $selectedCode) {
                return $voucher;
            }
        }

        return null;
    }

    private function isSelectedVoucherAvailable(): bool
    {
        return ! is_null($this->selectedVoucher());
    }

    private function selectedItems()
    {
        return collect($this->cartItems)->filter(function (array $item): bool {
            return in_array((int) ($item['id'] ?? 0), $this->selectedCartItemIds, true);
        });
    }

    private function cartQuery(): Builder
    {
        return Cart::query()->where(function (Builder $query): void {
            if (Auth::check()) {
                $query->where('user_id', (int) Auth::id());

                return;
            }

            $query->whereNull('user_id')->where('session_id', session()->getId());
        });
    }

    private function belongsToActor(Cart $cart): bool
    {
        if (Auth::check()) {
            return (int) $cart->user_id === (int) Auth::id();
        }

        return is_null($cart->user_id) && (string) $cart->session_id === (string) session()->getId();
    }

    private function formatVoucherLabel(Voucher $voucher): string
    {
        $discountType = (string) $voucher->discount_type;
        $discountValue = (float) $voucher->discount_value;

        return match ($discountType) {
            'percent' => sprintf('%s - giảm %s%%', $voucher->code, rtrim(rtrim(number_format($discountValue, 2, '.', ''), '0'), '.')),
            'shipping' => sprintf('%s - giảm phí ship %sđ', $voucher->code, number_format($discountValue, 0, ',', '.')),
            default => sprintf('%s - giảm %sđ', $voucher->code, number_format($discountValue, 0, ',', '.')),
        };
    }

    private function resolveProductImageUrl(?Products $product): string
    {
        $imagePath = (string) ($product?->main_image_url ?? '');

        if ($imagePath === '') {
            return 'https://placehold.co/240x320/f3f4f6/9ca3af?text=Product';
        }

        $normalizedPath = str_replace('\\', '/', trim($imagePath));

        if (Str::startsWith($normalizedPath, ['http://', 'https://'])) {
            return $normalizedPath;
        }

        if (Str::startsWith($normalizedPath, ['/storage/', 'storage/', '/uploads/', 'uploads/', '/images/', 'images/'])) {
            return asset(ltrim($normalizedPath, '/'));
        }

        return asset('storage/'.ltrim($normalizedPath, '/'));
    }
}
