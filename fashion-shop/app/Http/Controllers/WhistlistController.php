<?php

namespace App\Http\Controllers;

use App\Models\Whistlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhistlistController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()) {
            $this->mergeSessionToUser($request);
        }

        $products = $this->whistlistQuery($request)
            ->with('product')
            ->orderByDesc('id')
            ->get()
            ->pluck('product')
            ->filter(fn ($product) => $product && (int) $product->is_active === 1)
            ->values();

        return view('pages.user.whistlist.index', [
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        if ($request->user()) {
            Whistlist::query()->firstOrCreate([
                'user_id' => (int) $request->user()->id,
                'session_id' => null,
                'product_id' => (int) $validated['product_id'],
            ]);
        } else {
            Whistlist::query()->firstOrCreate([
                'user_id' => null,
                'session_id' => $request->session()->getId(),
                'product_id' => (int) $validated['product_id'],
            ]);
        }

        return back()->with('success', 'Đã thêm sản phẩm vào whistlist.');
    }

    public function destroy(Request $request, int $productId): RedirectResponse
    {
        $this->whistlistQuery($request)
            ->where('product_id', $productId)
            ->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi whistlist.');
    }

    private function whistlistQuery(Request $request): Builder
    {
        return Whistlist::query()->where(function (Builder $query) use ($request): void {
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

        Whistlist::query()
            ->whereNull('user_id')
            ->where('session_id', $sessionId)
            ->get()
            ->each(function (Whistlist $sessionItem) use ($userId): void {
                Whistlist::query()->firstOrCreate([
                    'user_id' => $userId,
                    'session_id' => null,
                    'product_id' => (int) $sessionItem->product_id,
                ]);

                $sessionItem->delete();
            });
    }
}
