<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Products;
use App\Models\ProductSkus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.user.cart.index');
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

        return back()->with('success', 'Đã cập nhật số lượng sản phẩm.');
    }

    public function remove(Request $request, Cart $cart): RedirectResponse
    {
        if (! $this->belongsToActor($request, $cart)) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
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
}
