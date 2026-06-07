<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Products;
use App\Models\ProductSkus;
use App\Services\VoucherService;
use App\Support\FlashSalePricing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private const SHIPPING_FEE = 30000;

    private const FREE_SHIPPING_THRESHOLD = 499000;

    public function index(Request $request)
    {
        $flashSales = FlashSalePricing::activeSales();

        $items = $this->cartQuery($request)
            ->with(['productSku.product'])
            ->orderByDesc('id')
            ->get();

        $cartItems = $items->map(function (Cart $item) use ($flashSales): array {
            $product = $item->productSku?->product;

            if ($product instanceof Products) {
                $product = FlashSalePricing::applyProduct($product, $flashSales);
            }

            $imagePath = (string) ($product?->main_image_url ?? '');
            if ($imagePath === '') {
                $imageUrl = 'https://placehold.co/240x320/f3f4f6/9ca3af?text=Product';
            } else {
                $normalizedPath = str_replace('\\', '/', trim($imagePath));
                if (Str::startsWith($normalizedPath, ['http://', 'https://'])) {
                    $imageUrl = $normalizedPath;
                } elseif (Str::startsWith($normalizedPath, ['/storage/', 'storage/', '/uploads/', 'uploads/', '/images/', 'images/'])) {
                    $imageUrl = asset(ltrim($normalizedPath, '/'));
                } else {
                    $imageUrl = asset('storage/'.ltrim($normalizedPath, '/'));
                }
            }

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

        $selectedIds = array_map('intval', (array) $request->query('selected_cart_ids', []));

        // compute totals
        $selectedItems = collect($cartItems)->filter(function (array $item) use ($selectedIds) {
            return in_array((int) ($item['id'] ?? 0), $selectedIds, true);
        });

        $subtotal = round((float) $selectedItems->sum('line_total'), 2);
        $shipping = $selectedItems->isEmpty() ? 0.0 : ($subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::SHIPPING_FEE);
        $discount = 0.0;

        // load vouchers for auth user
        $availableVouchers = [];
        if ($request->user()) {
            $voucherService = new VoucherService;
            $voucherService->cleanupExpiredAndUsedVouchers((int) $request->user()->id);
            $walletVouchers = $voucherService->getAvailableVouchersForUser((int) $request->user()->id);

            $availableVouchers = $walletVouchers->map(function ($userVoucher) {
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
                    'display' => (string) $this->formatVoucherLabel($voucher),
                ];
            })->filter()->values()->all();
        }

        // apply voucher if provided
        $selectedVoucherCode = trim((string) $request->query('voucher_code', ''));
        if ($selectedVoucherCode !== '' && ! empty($availableVouchers)) {
            $selectedVoucher = null;
            foreach ($availableVouchers as $v) {
                if (strtoupper($v['code']) === strtoupper($selectedVoucherCode)) {
                    $selectedVoucher = $v;
                    break;
                }
            }

            if ($selectedVoucher) {
                // calculate discount similar to Livewire logic
                if ($subtotal >= (float) ($selectedVoucher['min_order_value'] ?? 0)) {
                    if (($selectedVoucher['discount_type'] ?? '') === 'shipping') {
                        $discount = min((float) ($selectedVoucher['discount_value'] ?? 0), $shipping);
                    } else {
                        // eligible subtotal
                        $eligibleSubtotal = 0.0;
                        if (($selectedVoucher['category'] ?? 'all') === 'all') {
                            $eligibleSubtotal = (float) $selectedItems->sum('line_total');
                        } else {
                            $eligibleSubtotal = (float) $selectedItems->sum(function (array $item) use ($selectedVoucher) {
                                $matched = false;
                                switch ($selectedVoucher['category']) {
                                    case 'product':
                                        $matched = (int) ($selectedVoucher['product_id'] ?? 0) === (int) ($item['product_id'] ?? 0);
                                        break;
                                    case 'category':
                                        $matched = (int) ($selectedVoucher['category_id'] ?? 0) === (int) ($item['category_id'] ?? 0);
                                        break;
                                    case 'collection':
                                        $matched = (int) ($selectedVoucher['collection_id'] ?? 0) === (int) ($item['collection_id'] ?? 0);
                                        break;
                                }

                                return $matched ? (float) ($item['line_total'] ?? 0) : 0.0;
                            });
                        }

                        if ($eligibleSubtotal > 0) {
                            $discountValue = (float) ($selectedVoucher['discount_value'] ?? 0);
                            $calc = ($selectedVoucher['discount_type'] ?? '') === 'percent'
                                ? ($eligibleSubtotal * $discountValue) / 100
                                : $discountValue;

                            if (! is_null($selectedVoucher['max_discount'])) {
                                $calc = min($calc, (float) $selectedVoucher['max_discount']);
                            }

                            $discount = round(min($calc, $eligibleSubtotal), 2);
                        }
                    }
                } else {
                    // voucher not applicable, clear
                    $selectedVoucherCode = '';
                }
            } else {
                $selectedVoucherCode = '';
            }
        }

        $gross = round($subtotal + $shipping, 2);
        $discount = min($discount, $gross);
        $total = round(max($gross - $discount, 0), 2);

        return view('pages.user.cart.index', [
            'cartItems' => $cartItems,
            'selectedCartItemIds' => $selectedIds,
            'availableVouchers' => $availableVouchers,
            'selectedVoucherCode' => $selectedVoucherCode,
            'paymentMethod' => $request->query('payment_method', 'cod'),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
        ]);
    }

    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'integer', 'min:1'],
            'product_sku_id' => ['nullable', 'integer', 'min:1'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        if (empty($validated['product_id']) && empty($validated['product_sku_id'])) {
            $message = 'Thiếu thông tin sản phẩm để thêm vào giỏ hàng.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $quantity = (int) ($validated['quantity'] ?? 1);
        $productSku = $this->resolveSku($validated);

        if (! $productSku) {
            $message = 'Sản phẩm hiện không khả dụng.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        if ((int) $productSku->stock < 1) {
            $message = 'Sản phẩm đã hết hàng.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        if ($request->user()) {
            Cart::putForUser((int) $request->user()->id, (int) $productSku->id, $quantity);
        } else {
            Cart::putForSession($request->session()->getId(), (int) $productSku->id, $quantity);
        }

        if ($request->expectsJson()) {
            $count = (int) $this->cartQuery($request)->sum('quantity');

            return response()->json([
                'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, Cart $cart): RedirectResponse
    {
        if (! $this->belongsToActor($request, $cart)) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart->update([
            'quantity' => (int) $validated['quantity'],
        ]);

        if ($request->expectsJson()) {
            $item = Cart::query()->with('productSku.product')->find($cart->id);
            $flashSales = FlashSalePricing::activeSales();
            $product = $item->productSku?->product;
            if ($product instanceof Products) {
                $product = FlashSalePricing::applyProduct($product, $flashSales);
            }

            $unitPrice = $product instanceof Products ? (float) (FlashSalePricing::displayPrice($product) ?: $product->base_price) : 0.0;
            $lineTotal = round($unitPrice * (int) $item->quantity, 2);

            return response()->json([
                'success' => true,
                'cart_id' => (int) $item->id,
                'quantity' => (int) $item->quantity,
                'line_total' => $lineTotal,
            ]);
        }

        return back()->with('success', 'Đã cập nhật số lượng sản phẩm.');
    }

    public function remove(Request $request, Cart $cart): RedirectResponse
    {
        if (! $this->belongsToActor($request, $cart)) {
            abort(403);
        }

        $cart->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'cart_id' => (int) $cart->id]);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function totals(Request $request)
    {
        $selected = $request->input('selected_cart_ids', []);
        // support comma-separated string or array
        if (is_string($selected)) {
            $selected = array_filter(array_map('trim', explode(',', $selected)));
        }

        $selectedIds = array_map('intval', (array) $selected);

        $flashSales = FlashSalePricing::activeSales();

        $items = $this->cartQuery($request)
            ->with(['productSku.product'])
            ->orderByDesc('id')
            ->get();

        $cartItems = $items->map(function (Cart $item) use ($flashSales): array {
            $product = $item->productSku?->product;

            if ($product instanceof Products) {
                $product = FlashSalePricing::applyProduct($product, $flashSales);
            }

            $quantity = (int) $item->quantity;
            $basePrice = $product instanceof Products ? (float) ($product->base_price ?? 0) : 0.0;
            $salePrice = $product instanceof Products ? FlashSalePricing::displayPrice($product) : 0.0;
            $unitPrice = $salePrice > 0 ? $salePrice : $basePrice;

            return [
                'id' => (int) $item->id,
                'product_id' => (int) ($product?->id ?? 0),
                'category_id' => (int) ($product?->category_id ?? 0),
                'collection_id' => (int) ($product?->collection_id ?? 0),
                'line_total' => round($unitPrice * $quantity, 2),
            ];
        })->toArray();

        $selectedItems = collect($cartItems)->filter(function (array $item) use ($selectedIds) {
            return in_array((int) ($item['id'] ?? 0), $selectedIds, true);
        });

        $subtotal = round((float) $selectedItems->sum('line_total'), 2);
        $shipping = $selectedItems->isEmpty() ? 0.0 : ($subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::SHIPPING_FEE);
        $discount = 0.0;

        $availableVouchers = [];
        if ($request->user()) {
            $voucherService = new VoucherService;
            $voucherService->cleanupExpiredAndUsedVouchers((int) $request->user()->id);
            $walletVouchers = $voucherService->getAvailableVouchersForUser((int) $request->user()->id);

            $availableVouchers = $walletVouchers->map(function ($userVoucher) {
                $v = $userVoucher->voucher;
                if (! $v) {
                    return null;
                }

                return [
                    'id' => (int) $v->id,
                    'code' => (string) $v->code,
                    'discount_type' => (string) $v->discount_type,
                    'discount_value' => (float) $v->discount_value,
                    'min_order_value' => (float) ($v->min_order_value ?? 0),
                    'max_discount' => is_null($v->max_discount) ? null : (float) $v->max_discount,
                    'category' => (string) ($v->category ?? 'all'),
                    'product_id' => is_null($v->product_id) ? null : (int) $v->product_id,
                    'category_id' => is_null($v->category_id) ? null : (int) $v->category_id,
                    'collection_id' => is_null($v->collection_id) ? null : (int) $v->collection_id,
                    'display' => $this->formatVoucherLabel($v),
                ];
            })->filter()->values()->all();
        }

        $selectedVoucherCode = trim((string) $request->input('voucher_code', ''));
        if ($selectedVoucherCode !== '' && ! empty($availableVouchers)) {
            $selectedVoucher = null;
            foreach ($availableVouchers as $v) {
                if (strtoupper($v['code']) === strtoupper($selectedVoucherCode)) {
                    $selectedVoucher = $v;
                    break;
                }
            }

            if ($selectedVoucher) {
                if ($subtotal >= (float) ($selectedVoucher['min_order_value'] ?? 0)) {
                    if (($selectedVoucher['discount_type'] ?? '') === 'shipping') {
                        $discount = min((float) ($selectedVoucher['discount_value'] ?? 0), $shipping);
                    } else {
                        // compute eligible subtotal depending on voucher category
                        $eligibleSubtotal = 0.0;
                        if (($selectedVoucher['category'] ?? 'all') === 'all') {
                            $eligibleSubtotal = (float) $selectedItems->sum('line_total');
                        } else {
                            $eligibleSubtotal = (float) $selectedItems->sum(function (array $item) use ($selectedVoucher) {
                                $matched = false;
                                switch ($selectedVoucher['category']) {
                                    case 'product':
                                        $matched = (int) ($selectedVoucher['product_id'] ?? 0) === (int) ($item['product_id'] ?? 0);
                                        break;
                                    case 'category':
                                        $matched = (int) ($selectedVoucher['category_id'] ?? 0) === (int) ($item['category_id'] ?? 0);
                                        break;
                                    case 'collection':
                                        $matched = (int) ($selectedVoucher['collection_id'] ?? 0) === (int) ($item['collection_id'] ?? 0);
                                        break;
                                }

                                return $matched ? (float) ($item['line_total'] ?? 0) : 0.0;
                            });
                        }

                        if ($eligibleSubtotal > 0) {
                            $discountValue = (float) ($selectedVoucher['discount_value'] ?? 0);
                            $calc = ($selectedVoucher['discount_type'] ?? '') === 'percent'
                                ? ($eligibleSubtotal * $discountValue) / 100
                                : $discountValue;

                            if (! is_null($selectedVoucher['max_discount'])) {
                                $calc = min($calc, (float) $selectedVoucher['max_discount']);
                            }

                            $discount = round(min($calc, $eligibleSubtotal), 2);
                        }
                    }
                } else {
                    // not applicable
                    $selectedVoucherCode = '';
                }
            } else {
                $selectedVoucherCode = '';
            }
        }

        $gross = round($subtotal + $shipping, 2);
        $discount = min($discount, $gross);
        $total = round(max($gross - $discount, 0), 2);

        return response()->json([
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'availableVouchers' => $availableVouchers,
            'selectedCount' => count($selectedIds),
        ]);
    }

    public function count(Request $request): JsonResponse
    {
        $count = (int) $this->cartQuery($request)->sum('quantity');

        return response()->json(['count' => $count]);
    }

    public function productSkus(int $productId): JsonResponse
    {
        $product = Products::query()
            ->where('id', $productId)
            ->first(['id', 'name', 'slug', 'base_price', 'main_image_url']);

        $skus = ProductSkus::query()
            ->where('product_id', $productId)
            ->orderByDesc('stock')
            ->orderBy('id')
            ->get(['id', 'sku', 'size', 'color', 'stock']);

        $productPayload = null;

        if ($product) {
            $imagePath = (string) ($product->main_image_url ?? '');

            if ($imagePath === '') {
                $imageUrl = 'https://placehold.co/600x800/f3f4f6/9ca3af?text=Product';
            } else {
                $normalizedPath = str_replace('\\', '/', trim($imagePath));

                if (Str::startsWith($normalizedPath, ['http://', 'https://'])) {
                    $imageUrl = $normalizedPath;
                } elseif (Str::startsWith($normalizedPath, ['/storage/', 'storage/', '/uploads/', 'uploads/', '/images/', 'images/'])) {
                    $imageUrl = asset(ltrim($normalizedPath, '/'));
                } else {
                    $imageUrl = asset('storage/'.ltrim($normalizedPath, '/'));
                }
            }

            $productPayload = [
                'id' => (int) $product->id,
                'name' => (string) $product->name,
                'slug' => (string) ($product->slug ?? ''),
                'base_price' => (float) ($product->base_price ?? 0),
                'image_url' => $imageUrl,
                'detail_url' => route('user.product-detail', ['product' => $product->slug ?: $product->id]),
            ];
        }

        return response()->json([
            'product_id' => $productId,
            'product' => $productPayload,
            'skus' => $skus,
        ]);
    }

    private function cartQuery(Request $request): Builder
    {
        return Cart::query()->where(function (Builder $query) use ($request): void {
            if ($request->user()) {
                $query->where('user_id', (int) $request->user()->id);

                return;
            }

            $query->whereNull('user_id')->where('session_id', $request->session()->getId());
        });
    }

    private function belongsToActor(Request $request, Cart $cart): bool
    {
        if ($request->user()) {
            return (int) $cart->user_id === (int) $request->user()->id;
        }

        return is_null($cart->user_id) && (string) $cart->session_id === (string) $request->session()->getId();
    }

    private function resolveSku(array $validated): ?ProductSkus
    {
        if (! empty($validated['product_sku_id'])) {
            return ProductSkus::query()->find((int) $validated['product_sku_id']);
        }

        return ProductSkus::query()
            ->where('product_id', (int) $validated['product_id'])
            ->orderByDesc('stock')
            ->orderBy('id')
            ->first();
    }

    private function formatVoucherLabel($voucher): string
    {
        $discountType = (string) $voucher->discount_type;
        $discountValue = (float) $voucher->discount_value;

        return match ($discountType) {
            'percent' => sprintf('%s - giảm %s%%', $voucher->code, rtrim(rtrim(number_format($discountValue, 2, '.', ''), '0'), '.')),
            'shipping' => sprintf('%s - giảm phí ship %sđ', $voucher->code, number_format($discountValue, 0, ',', '.')),
            default => sprintf('%s - giảm %sđ', $voucher->code, number_format($discountValue, 0, ',', '.')),
        };
    }
}
