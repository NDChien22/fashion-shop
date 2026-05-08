<?php

namespace App\Livewire\User;

use App\Models\Cart;
use App\Models\Products;
use App\Models\ProductSkus;
use App\Models\OrderFeedback;
use App\Models\Whistlist;
use App\Support\FlashSalePricing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ProductDetail extends Component
{
    public Products $product;

    public float $basePrice = 0.0;

    public ?float $salePrice = null;

    public bool $hasSalePrice = false;

    public int $saleDiscountPercent = 0;

    public array $images = [];

    public array $colorOptions = [];

    public array $sizeOptions = [];

    public string $activeImage = '';

    public int $selectedQuantity = 1;

    public string $selectedColor = '';

    public string $selectedSize = '';

    public bool $isWishlisted = false;

    public ?int $selectedSkuId = null;

    public function mount(Products $product)
    {
        $this->product = $product->loadMissing(['category', 'collection', 'skus']);
        FlashSalePricing::applyProduct($this->product);
        $this->basePrice = (float) ($this->product->base_price ?? 0);
        $this->salePrice = FlashSalePricing::displayPrice($this->product);
        $this->hasSalePrice = FlashSalePricing::hasSale($this->product);
        $this->saleDiscountPercent = FlashSalePricing::discountPercent($this->product, $this->salePrice);
        $this->images = $this->buildImages();
        $this->activeImage = $this->images[0] ?? asset('images/placeholder.png');
        $this->colorOptions = $this->buildColorOptions();
        $this->sizeOptions = $this->buildSizeOptions();

        $firstSku = $this->product->skus->sortByDesc('stock')->first();
        $this->selectedSkuId = $firstSku?->id;

        if ($firstSku) {
            $this->selectedColor = (string) ($firstSku->color ?? '');
            $this->selectedSize = (string) ($firstSku->size ?? '');
        }

        $this->syncSelectedSku();
        $this->refreshWishlistState();
    }

    public function setActiveImage(string $image): void
    {
        $this->activeImage = $image;
    }

    public function selectSku(int $skuId): void
    {
        $sku = $this->product->skus->firstWhere('id', $skuId);

        if (! $sku) {
            return;
        }

        $this->selectedSkuId = $skuId;
        $this->selectedColor = (string) ($sku->color ?? '');
        $this->selectedSize = (string) ($sku->size ?? '');
        $this->selectedQuantity = 1;
    }

    public function selectColor(string $color): void
    {
        $this->selectedColor = trim($color);
        $this->syncSelectedSku();
    }

    public function selectSize(string $size): void
    {
        $this->selectedSize = strtoupper(trim($size));
        $this->syncSelectedSku();
    }

    public function increaseQuantity(): void
    {
        $this->selectedQuantity++;
        $this->clampQuantityToStock();
    }

    public function decreaseQuantity(): void
    {
        $this->selectedQuantity = max(1, $this->selectedQuantity - 1);
    }

    public function updatedSelectedQuantity(): void
    {
        $this->selectedQuantity = max(1, (int) $this->selectedQuantity);
        $this->clampQuantityToStock();
    }

    public function addToCart(): void
    {
        $sku = $this->currentSku();

        if (! $sku) {
            $this->dispatch('app-toast', message: 'Sản phẩm hiện chưa có phiên bản khả dụng.', type: 'error');

            return;
        }

        if ((int) $sku->stock < 1) {
            $this->dispatch('app-toast', message: 'Phiên bản sản phẩm này đã hết hàng.', type: 'error');

            return;
        }

        if (Auth::check()) {
            Cart::putForUser((int) Auth::id(), (int) $sku->id, $this->selectedQuantity);
        } else {
            Cart::putForSession((string) request()->session()->getId(), (int) $sku->id, $this->selectedQuantity);
        }

        $cartCount = (int) $this->cartQuery()->sum('quantity');

        $this->dispatch('cart-count-updated', count: $cartCount);
        $this->dispatch('app-toast', message: 'Đã thêm vào giỏ hàng!', type: 'success');
        $this->selectedQuantity = 1;
    }

    public function toggleWhistlist(): void
    {
        $existing = $this->whistlistQuery()
            ->where('product_id', (int) $this->product->id)
            ->first();

        if ($existing) {
            $this->whistlistQuery()
                ->where('product_id', (int) $this->product->id)
                ->delete();

            $this->isWishlisted = false;
            $message = 'Đã xóa sản phẩm khỏi whistlist.';
        } else {
            Whistlist::query()->firstOrCreate([
                'user_id' => Auth::check() ? (int) Auth::id() : null,
                'session_id' => Auth::check() ? null : request()->session()->getId(),
                'product_id' => (int) $this->product->id,
            ]);

            $this->isWishlisted = true;
            $message = 'Đã thêm sản phẩm vào whistlist.';
        }

        $count = $this->whistlistQuery()
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->count();

        $this->dispatch('whistlist-count-updated', count: (int) $count);
        $this->dispatch('app-toast', message: $message, type: 'success');
    }

    public function render()
    {
        $reviewsQuery = OrderFeedback::query()->where('product_id', $this->product->id);

        $reviews = $reviewsQuery
            ->with([
                'user:id,username,full_name',
                'order:id,order_code,created_at',
            ])
            ->latest()
            ->paginate(5);

        $relatedProducts = Products::query()
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->limit(4)
            ->get();

        $relatedProducts = FlashSalePricing::applyProducts($relatedProducts);

        $selectedSku = $this->currentSku();

        return view('livewire.user.product-detail', [
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts,
            'selectedSku' => $selectedSku,
            'reviewCount' => (int) $reviewsQuery->count(),
            'averageRating' => round((float) $reviewsQuery->avg('rating'), 1),
            'totalStock' => (int) $this->product->skus->sum('stock'),
            'variants' => $this->product->skus->sortByDesc('stock')->values(),
        ]);
    }

    private function currentSku(): ?ProductSkus
    {
        if ($this->selectedSkuId === null) {
            return $this->product->skus->sortByDesc('stock')->first();
        }

        return $this->product->skus->firstWhere('id', $this->selectedSkuId);
    }

    private function clampQuantityToStock(): void
    {
        $sku = $this->currentSku();

        if (! $sku) {
            return;
        }

        $this->selectedQuantity = min($this->selectedQuantity, max(1, (int) $sku->stock));
    }

    private function syncSelectedSku(): void
    {
        $matchedSku = $this->product->skus->first(function (ProductSkus $sku): bool {
            if ((int) $sku->stock < 1) {
                return false;
            }

            if ($this->selectedColor !== '' && trim((string) $sku->color) !== $this->selectedColor) {
                return false;
            }

            if ($this->selectedSize !== '' && strtoupper(trim((string) $sku->size)) !== $this->selectedSize) {
                return false;
            }

            return true;
        });

        $this->selectedSkuId = $matchedSku?->id;

        if ($this->selectedSkuId !== null) {
            $this->selectedQuantity = 1;
        }
    }

    private function buildColorOptions(): array
    {
        return $this->product->skus
            ->pluck('color')
            ->map(fn ($color) => trim((string) $color))
            ->filter(fn (string $color) => $color !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function buildSizeOptions(): array
    {
        return $this->product->skus
            ->pluck('size')
            ->map(fn ($size) => strtoupper(trim((string) $size)))
            ->filter(fn (string $size) => $size !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function buildImages(): array
    {
        $paths = array_filter(array_merge([
            $this->product->main_image_url,
        ], $this->product->gallery_image_urls ?? []));

        $images = [];

        foreach ($paths as $path) {
            $normalized = $this->normalizeImageUrl((string) $path);
            if ($normalized && ! in_array($normalized, $images, true)) {
                $images[] = $normalized;
            }
        }

        if ($images === []) {
            $images[] = asset('images/placeholder.png');
        }

        return $images;
    }

    private function normalizeImageUrl(string $path): ?string
    {
        $path = trim(str_replace('\\', '/', $path));

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['/storage/', 'storage/', '/uploads/', 'uploads/', '/images/', 'images/'])) {
            return asset(ltrim($path, '/'));
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    private function cartQuery(): Builder
    {
        return Cart::query()->where(function (Builder $query): void {
            if (Auth::check()) {
                $query->where('user_id', (int) Auth::id());

                return;
            }

            $query->whereNull('user_id')->where('session_id', request()->session()->getId());
        });
    }

    private function whistlistQuery(): Builder
    {
        return Whistlist::query()->where(function (Builder $query): void {
            if (Auth::check()) {
                $query->where('user_id', (int) Auth::id());

                return;
            }

            $query->whereNull('user_id')->where('session_id', request()->session()->getId());
        });
    }

    private function refreshWishlistState(): void
    {
        $this->isWishlisted = $this->whistlistQuery()
            ->where('product_id', (int) $this->product->id)
            ->exists();
    }
}
