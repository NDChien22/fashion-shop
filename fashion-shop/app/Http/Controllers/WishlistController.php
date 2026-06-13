<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Support\FlashSalePricing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()) {
            $this->mergeSessionToUser($request);
        }

        $products = $this->wishlistQuery($request)
            ->with('product')
            ->orderByDesc('id')
            ->get()
            ->pluck('product')
            ->filter(fn ($product) => $product && (int) $product->is_active === 1)
            ->values();

        $products = FlashSalePricing::applyProducts($products);

        return view('pages.user.wishlist.index', [
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        Wishlist::query()->firstOrCreate([
            'user_id' => $request->user() ? (int) $request->user()->id : null,
            'session_id' => $request->user() ? null : $request->session()->getId(),
            'product_id' => (int) $validated['product_id'],
        ]);

        return back()->with('success', 'Da them san pham vao wishlist.');
    }

    public function destroy(Request $request, int $productId): RedirectResponse
    {
        $this->wishlistQuery($request)
            ->where('product_id', $productId)
            ->delete();

        return back()->with('success', 'Da xoa san pham khoi wishlist.');
    }

    private function wishlistQuery(Request $request): Builder
    {
        return Wishlist::query()->where(function (Builder $query) use ($request): void {
            if ($request->user()) {
                $query->where('user_id', (int) $request->user()->id);

                return;
            }

            $query->whereNull('user_id')
                ->where('session_id', $request->session()->getId());
        });
    }

    private function mergeSessionToUser(Request $request): void
    {
        $userId = (int) $request->user()->id;
        $sessionId = $request->session()->getId();

        Wishlist::query()
            ->whereNull('user_id')
            ->where('session_id', $sessionId)
            ->get()
            ->each(function (Wishlist $sessionItem) use ($userId): void {
                Wishlist::query()->firstOrCreate([
                    'user_id' => $userId,
                    'session_id' => null,
                    'product_id' => (int) $sessionItem->product_id,
                ]);

                $sessionItem->delete();
            });
    }
}
