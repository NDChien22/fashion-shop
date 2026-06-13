<?php

namespace App\Livewire\User;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductQuickActions extends Component
{
    public int $productId;

    public bool $wishlisted = false;

    public bool $updatingWishlist = false;

    public function mount(int $productId, bool $wishlisted = false): void
    {
        $this->productId = $productId;
        $this->wishlisted = $wishlisted;
    }

    public function openSkuPicker(): void
    {
        $this->dispatch('open-sku-picker', productId: $this->productId)
            ->to(SkuPickerModal::class);
    }

    public function toggleWishlist(): void
    {
        if ($this->updatingWishlist) {
            return;
        }

        $this->updatingWishlist = true;

        $existing = $this->wishlistQuery()
            ->where('product_id', $this->productId)
            ->first();

        if ($existing) {
            // Remove all duplicated wishlist rows for this product under current actor.
            $this->wishlistQuery()
                ->where('product_id', $this->productId)
                ->delete();

            $this->wishlisted = false;
            $message = 'Đã xóa sản phẩm khỏi wishlist.';
            $this->dispatch('wishlist-item-removed', productId: $this->productId);
        } else {
            Wishlist::query()->firstOrCreate([
                'user_id' => Auth::check() ? (int) Auth::id() : null,
                'session_id' => Auth::check() ? null : request()->session()->getId(),
                'product_id' => $this->productId,
            ]);

            $this->wishlisted = true;
            $message = 'Đã thêm sản phẩm vào wishlist.';
        }

        $count = $this->wishlistQuery()
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->count();

        $this->dispatch('wishlist-count-updated', count: (int) $count);
        $this->dispatch('app-toast', message: $message, type: 'success');

        $this->updatingWishlist = false;
    }

    private function wishlistQuery(): Builder
    {
        return Wishlist::query()->where(function (Builder $query): void {
            if (Auth::check()) {
                $query->where('user_id', (int) Auth::id());

                return;
            }

            $query->whereNull('user_id')
                ->where('session_id', request()->session()->getId());
        });
    }

    public function render()
    {
        return view('livewire.user.product-quick-actions');
    }
}
