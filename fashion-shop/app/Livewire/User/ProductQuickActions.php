<?php

namespace App\Livewire\User;

use App\Models\Whistlist;
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

        $existing = $this->whistlistQuery()
            ->where('product_id', $this->productId)
            ->first();

        if ($existing) {
            // Remove all duplicated wishlist rows for this product under current actor.
            $this->whistlistQuery()
                ->where('product_id', $this->productId)
                ->delete();

            $this->wishlisted = false;
            $message = 'Đã xóa sản phẩm khỏi whistlist.';
            $this->dispatch('wishlist-item-removed', productId: $this->productId);
        } else {
            Whistlist::query()->firstOrCreate([
                'user_id' => Auth::check() ? (int) Auth::id() : null,
                'session_id' => Auth::check() ? null : request()->session()->getId(),
                'product_id' => $this->productId,
            ]);

            $this->wishlisted = true;
            $message = 'Đã thêm sản phẩm vào whistlist.';
        }

        $count = $this->whistlistQuery()
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->count();

        $this->dispatch('whistlist-count-updated', count: (int) $count);
        $this->dispatch('app-toast', message: $message, type: 'success');

        $this->updatingWishlist = false;
    }

    private function whistlistQuery(): Builder
    {
        return Whistlist::query()->where(function (Builder $query): void {
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
