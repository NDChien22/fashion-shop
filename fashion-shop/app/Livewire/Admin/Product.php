<?php

namespace App\Livewire\Admin;

use App\Models\Categories;
use App\Models\Collections;
use App\Models\Products;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Product extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryId = '';

    public string $collectionId = '';

    public ?array $selectedProduct = null;

    public ?string $selectedDetailImage = null;

    public bool $showProductDetailModal = false;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'categoryId' => ['except' => '', 'as' => 'category'],
        'collectionId' => ['except' => '', 'as' => 'collection'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingCollectionId(): void
    {
        $this->resetPage();
    }

    public function deleteProduct(int $productId): void
    {
        $product = Products::query()
            ->with('skus:id,product_id')
            ->find($productId);

        if (! $product) {
            session()->flash('error', 'Sản phẩm không tồn tại hoặc đã bị xóa.');
            return;
        }

        DB::transaction(function () use ($product) {
            $this->deleteImageFromPublicDisk($product->main_image_url);

            $galleryImages = is_array($product->gallery_image_urls) ? $product->gallery_image_urls : [];
            foreach ($galleryImages as $imagePath) {
                $this->deleteImageFromPublicDisk($imagePath);
            }

            $product->skus()->delete();
            $product->delete();
        });

        session()->flash('success', 'Xóa sản phẩm thành công.');
    }

    public function showProductDetail(int $productId): void
    {
        $product = Products::query()
            ->with([
                'category:id,name',
                'collection:id,name',
                'skus:id,product_id,size,color,stock',
            ])
            ->find($productId);

        if (! $product) {
            session()->flash('error', 'Sản phẩm không tồn tại.');
            return;
        }

        $mainImage = $this->resolveImageUrl($product->main_image_url) ?? '/asset/img/product-1.jpg';

        $galleryImages = collect(is_array($product->gallery_image_urls) ? $product->gallery_image_urls : [])
            ->filter(fn ($path) => is_string($path) && trim($path) !== '')
            ->map(fn (string $path) => $this->resolveImageUrl($path))
            ->filter()
            ->values()
            ->all();

        $previewImages = collect([$mainImage])
            ->merge($galleryImages)
            ->unique()
            ->values()
            ->all();

        $this->selectedProduct = [
            'id' => $product->id,
            'product_code' => $product->product_code,
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category?->name,
            'collection' => $product->collection?->name,
            'base_price' => (float) $product->base_price,
            'main_image' => $mainImage,
            'preview_images' => $previewImages,
            'is_active' => (bool) $product->is_active,
            'updated_at' => $product->updated_at?->diffForHumans(),
            'skus' => $product->skus
                ->map(fn ($sku) => [
                    'size' => $sku->size,
                    'color' => $sku->color,
                    'stock' => (int) $sku->stock,
                ])
                ->all(),
        ];

        $this->selectedDetailImage = $previewImages[0] ?? $mainImage;

        $this->showProductDetailModal = true;
    }

    public function setDetailImage(int $index): void
    {
        if (! $this->selectedProduct || ! isset($this->selectedProduct['preview_images'][$index])) {
            return;
        }

        $this->selectedDetailImage = $this->selectedProduct['preview_images'][$index];
    }

    public function showPreviousDetailImage(): void
    {
        if (! $this->selectedProduct || empty($this->selectedProduct['preview_images'])) {
            return;
        }

        $images = $this->selectedProduct['preview_images'];
        $currentIndex = $this->getSelectedDetailImageIndex();
        $previousIndex = $currentIndex > 0 ? $currentIndex - 1 : count($images) - 1;

        $this->selectedDetailImage = $images[$previousIndex];
    }

    public function showNextDetailImage(): void
    {
        if (! $this->selectedProduct || empty($this->selectedProduct['preview_images'])) {
            return;
        }

        $images = $this->selectedProduct['preview_images'];
        $currentIndex = $this->getSelectedDetailImageIndex();
        $nextIndex = $currentIndex < count($images) - 1 ? $currentIndex + 1 : 0;

        $this->selectedDetailImage = $images[$nextIndex];
    }

    public function closeProductDetailModal(): void
    {
        $this->showProductDetailModal = false;
        $this->selectedDetailImage = null;
    }

    private function deleteImageFromPublicDisk(?string $path): void
    {
        if (! $path) {
            return;
        }

        $normalizedPath = ltrim($path, '/');
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if ($normalizedPath !== '' && Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }
    }

    private function resolveImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalizedPath = ltrim($path, '/');
        if (str_starts_with($normalizedPath, 'http://') || str_starts_with($normalizedPath, 'https://')) {
            return $normalizedPath;
        }

        return asset($normalizedPath);
    }

    private function getSelectedDetailImageIndex(): int
    {
        if (! $this->selectedProduct || empty($this->selectedProduct['preview_images'])) {
            return 0;
        }

        $currentImage = $this->selectedDetailImage ?: $this->selectedProduct['main_image'];
        $index = array_search($currentImage, $this->selectedProduct['preview_images'], true);

        return $index === false ? 0 : $index;
    }

    public function render()
    {
        $products = Products::query()
            ->with([
                'category:id,name',
                'collection:id,name',
            ])
            ->withSum('skus as total_stock', 'stock')
            ->when($this->search !== '', function ($query) {
                $keyword = trim($this->search);

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('product_code', 'like', '%' . $keyword . '%');
                });
            })
            ->when($this->categoryId !== '', function ($query) {
                $selectedCategory = Categories::query()
                    ->with('children:id,parent_id')
                    ->find($this->categoryId);

                if (! $selectedCategory) {
                    return;
                }

                $categoryIds = [$selectedCategory->id];
                if ($selectedCategory->children->isNotEmpty()) {
                    $categoryIds = array_merge(
                        $categoryIds,
                        $selectedCategory->children->pluck('id')->all()
                    );
                }

                $query->whereIn('category_id', $categoryIds);
            })
            ->when($this->collectionId !== '', function ($query) {
                $query->where('collection_id', $this->collectionId);
            })
            ->latest()
            ->paginate(10);

        $categories = Categories::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $collections = Collections::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.product', [
            'products' => $products,
            'categories' => $categories,
            'collections' => $collections,
        ]);
    }
}
