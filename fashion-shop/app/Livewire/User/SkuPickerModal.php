<?php

namespace App\Livewire\User;

use App\Models\Cart;
use App\Models\Products;
use App\Models\ProductSkus;
use App\Support\FlashSalePricing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class SkuPickerModal extends Component
{
    public bool $isOpen = false;

    public ?Products $product = null;

    public float $basePrice = 0.0;

    public ?float $salePrice = null;

    public bool $hasSalePrice = false;

    public int $saleDiscountPercent = 0;

    /** @var array<int, array{id:int, sku:string, size:string, color:string, stock:int}> */
    public array $skus = [];

    /** @var array<int, string> */
    public array $colorOptions = [];

    /** @var array<int, string> */
    public array $sizeOptions = [];

    public string $selectedColor = '';

    public string $selectedSize = '';

    public ?int $selectedSkuId = null;

    public string $statusMessage = '';

    #[On('open-sku-picker')]
    public function openSkuPicker(int $productId): void
    {
        $product = Products::query()
            ->where('id', $productId)
            ->where('is_active', 1)
            ->first();

        if (! $product) {
            $this->dispatch('app-toast', message: 'Không tìm thấy sản phẩm.', type: 'error');

            return;
        }

        $rawSkus = ProductSkus::query()
            ->where('product_id', $productId)
            ->orderByDesc('stock')
            ->orderBy('id')
            ->get(['id', 'sku', 'size', 'color', 'stock']);

        $this->product = $product;
        FlashSalePricing::applyProduct($this->product);
        $this->basePrice = (float) ($this->product->base_price ?? 0);
        $this->salePrice = FlashSalePricing::displayPrice($this->product);
        $this->hasSalePrice = FlashSalePricing::hasSale($this->product);
        $this->saleDiscountPercent = FlashSalePricing::discountPercent($this->product, $this->salePrice);
        $this->skus = $rawSkus->map(function (ProductSkus $sku): array {
            return [
                'id' => (int) $sku->id,
                'sku' => (string) ($sku->sku ?? ''),
                'size' => strtoupper(trim((string) ($sku->size ?? ''))),
                'color' => trim((string) ($sku->color ?? '')),
                'stock' => max(0, (int) ($sku->stock ?? 0)),
            ];
        })->values()->all();

        $this->colorOptions = collect($this->skus)
            ->pluck('color')
            ->filter(fn (string $color) => $color !== '')
            ->unique()
            ->values()
            ->all();

        $sizePriority = ['XS' => 1, 'S' => 2, 'M' => 3, 'L' => 4, 'XL' => 5, 'XXL' => 6, 'XXXL' => 7];

        $this->sizeOptions = collect($this->skus)
            ->pluck('size')
            ->filter(fn (string $size) => $size !== '')
            ->unique()
            ->sortBy(fn (string $size) => $sizePriority[$size] ?? (100 + strlen($size)))
            ->values()
            ->all();

        $this->selectedColor = '';
        $this->selectedSize = '';
        $this->statusMessage = 'Vui lòng chọn thuộc tính để xem SKU tương ứng.';
        $this->isOpen = true;

        $this->syncSelectedSku();
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->selectedSkuId = null;
        $this->statusMessage = '';
    }

    public function selectColor(string $color): void
    {
        $this->selectedColor = trim($color);
        $this->syncSelectedSku();
    }

    public function updatedSelectedColor(string $value): void
    {
        $this->selectedColor = trim($value);
        $this->syncSelectedSku();
    }

    public function selectSize(string $size): void
    {
        $this->selectedSize = strtoupper(trim($size));
        $this->syncSelectedSku();
    }

    public function updatedSelectedSize(string $value): void
    {
        $this->selectedSize = strtoupper(trim($value));
        $this->syncSelectedSku();
    }

    public function addToCart(): void
    {
        if (! $this->selectedSkuId) {
            $this->statusMessage = 'Vui lòng chọn phiên bản còn hàng.';

            return;
        }

        $sku = ProductSkus::query()->find($this->selectedSkuId);

        if (! $sku || (int) $sku->stock < 1) {
            $this->statusMessage = 'Phiên bản đã hết hàng hoặc không còn tồn tại.';

            return;
        }

        if (Auth::check()) {
            Cart::putForUser((int) Auth::id(), (int) $sku->id, 1);
        } else {
            Cart::putForSession(request()->session()->getId(), (int) $sku->id, 1);
        }

        $count = (int) $this->cartQuery()->sum('quantity');

        $this->dispatch('cart-count-updated', count: $count);
        $this->dispatch('app-toast', message: 'Đã thêm sản phẩm vào giỏ hàng.', type: 'success');

        $this->closeModal();
    }

    public function getDisplayPriceProperty(): float
    {
        return $this->salePrice ?? 0.0;
    }

    public function getHasSalePriceProperty(): bool
    {
        return $this->hasSalePrice;
    }

    public function getSelectedSkuLabelProperty(): string
    {
        if (! $this->selectedSkuId) {
            return '-';
        }

        $sku = collect($this->skus)->firstWhere('id', $this->selectedSkuId);

        return (string) ($sku['sku'] ?? '-');
    }

    public function getProductImageUrlProperty(): string
    {
        $path = (string) ($this->product?->main_image_url ?? '');

        if ($path === '') {
            return 'https://placehold.co/600x800/f3f4f6/9ca3af?text=Product';
        }

        $normalizedPath = str_replace('\\', '/', trim($path));

        if (Str::startsWith($normalizedPath, ['http://', 'https://'])) {
            return $normalizedPath;
        }

        if (Str::startsWith($normalizedPath, ['/storage/', 'storage/', '/uploads/', 'uploads/', '/images/', 'images/'])) {
            return asset(ltrim($normalizedPath, '/'));
        }

        return asset('storage/'.ltrim($normalizedPath, '/'));
    }

    private function syncSelectedSku(): void
    {
        $requiresColor = ! empty($this->colorOptions);
        $requiresSize = ! empty($this->sizeOptions);

        if ($requiresColor && $this->selectedColor === '') {
            $this->selectedSkuId = null;
            $this->statusMessage = 'Vui lòng chọn màu sắc.';

            return;
        }

        if ($requiresSize && $this->selectedSize === '') {
            $this->selectedSkuId = null;
            $this->statusMessage = 'Vui lòng chọn kích cỡ.';

            return;
        }

        $matched = collect($this->skus)
            ->first(function (array $sku): bool {
                if (($sku['stock'] ?? 0) < 1) {
                    return false;
                }

                if ($this->selectedColor !== '' && ($sku['color'] ?? '') !== $this->selectedColor) {
                    return false;
                }

                if ($this->selectedSize !== '' && ($sku['size'] ?? '') !== $this->selectedSize) {
                    return false;
                }

                return true;
            });

        $this->selectedSkuId = $matched['id'] ?? null;

        if ($this->selectedSkuId) {
            $this->statusMessage = 'SKU khả dụng. Bạn có thể thêm vào giỏ hàng.';

            return;
        }

        $this->statusMessage = 'Phiên bản đã chọn đang hết hàng, vui lòng chọn lại.';
    }

    private function cartQuery(): Builder
    {
        return Cart::query()->where(function (Builder $query): void {
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
        return view('livewire.user.sku-picker-modal');
    }
}
