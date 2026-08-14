<?php

namespace App\Livewire\User;

use App\Enums\VoucherStatus;
use App\Models\Categories;
use App\Models\FlashSale;
use App\Models\Products;
use App\Models\ProductSkus;
use App\Models\UserVoucher;
use App\Models\Voucher;
use App\Services\VoucherService;
use App\Support\FlashSalePricing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductListing extends Component
{
    use WithPagination;

    #[Url]
    public string $category = '';

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'newest';

    #[Url]
    public array $selectedCategories = [];

    #[Url]
    public string $priceRange = 'all';

    #[Url]
    public array $sizes = [];

    #[Url]
    public string $filter = '';

    public function mount(): void
    {
        $this->applyCategoryFilterFromQuery();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatingPriceRange()
    {
        $this->resetPage();
    }

    public function updatingSizes()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function toggleCategory($categoryId)
    {
        $categoryId = (int) $categoryId;

        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_filter($this->selectedCategories, fn ($id) => $id !== $categoryId);
        } else {
            $this->selectedCategories[] = $categoryId;
        }

        $this->selectedCategories = array_values(array_unique(array_map('intval', $this->selectedCategories)));

        $this->resetPage();
    }

    public function toggleSize(string $size): void
    {
        $normalizedSize = strtoupper(trim($size));

        if ($normalizedSize === '') {
            return;
        }

        if (in_array($normalizedSize, $this->sizes, true)) {
            $this->sizes = array_values(array_filter(
                $this->sizes,
                fn ($value) => $value !== $normalizedSize
            ));
        } else {
            $this->sizes[] = $normalizedSize;
        }

        $this->sizes = array_values(array_unique($this->sizes));

        $this->resetPage();
    }

    public function setSort(string $sort): void
    {
        $allowedSorts = array_keys($this->getSortOptions());

        $this->sort = in_array($sort, $allowedSorts, true) ? $sort : 'newest';

        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->category = '';
        $this->search = '';
        $this->sort = 'newest';
        $this->selectedCategories = [];
        $this->priceRange = 'all';
        $this->sizes = [];
        $this->filter = '';
        $this->resetPage();
    }

    public function render()
    {
        $sortOptions = $this->getSortOptions();
        $priceRangeOptions = $this->getPriceRangeOptions();

        $categoryOptions = Categories::query()
            ->where('is_active', 1)
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        $childrenByParent = $categoryOptions
            ->whereNotNull('parent_id')
            ->groupBy('parent_id');

        $categoryTree = $categoryOptions
            ->whereNull('parent_id')
            ->values()
            ->map(function ($parent) use ($childrenByParent): array {
                return [
                    'parent' => $parent,
                    'children' => $childrenByParent->get($parent->id, collect())->values(),
                ];
            });

        // Filter: flash-sale — chỉ lấy sản phẩm đang có flash sale đang hoạt động
        if ($this->filter === 'flash-sale') {
            $activeFlashSales = FlashSale::query()
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->get();

            $hasAllScope = $activeFlashSales->contains(fn ($s) => $s->scope === 'all');

            $flashSaleProductIds = null;
            $flashSaleCategoryIds = [];
            $flashSaleCollectionIds = [];

            if (! $hasAllScope) {
                $flashSaleProductIds = $activeFlashSales->where('scope', 'product')->pluck('product_id')->filter()->unique()->all();
                $flashSaleCategoryIds = $activeFlashSales->where('scope', 'category')->pluck('category_id')->filter()->unique()->all();
                $flashSaleCollectionIds = $activeFlashSales->where('scope', 'collection')->pluck('collection_id')->filter()->unique()->all();
            }

            $query = Products::query()->where('is_active', 1);

            if (! $hasAllScope) {
                $query->where(function ($q) use ($flashSaleProductIds, $flashSaleCategoryIds, $flashSaleCollectionIds): void {
                    if (! empty($flashSaleProductIds)) {
                        $q->orWhereIn('id', $flashSaleProductIds);
                    }

                    if (! empty($flashSaleCategoryIds)) {
                        $q->orWhereIn('category_id', $flashSaleCategoryIds);
                    }

                    if (! empty($flashSaleCollectionIds)) {
                        $q->orWhereIn('collection_id', $flashSaleCollectionIds);
                    }
                });
            }
        } elseif ($this->filter === 'best-seller') {
            $soldSubQuery = DB::table('order_items')
                ->join('product_skuses', 'product_skuses.id', '=', 'order_items.product_sku_id')
                ->select('product_skuses.product_id', DB::raw('SUM(order_items.quantity) as sold_qty'))
                ->groupBy('product_skuses.product_id');

            $query = Products::query()
                ->leftJoinSub($soldSubQuery, 'sold_products', fn ($join) => $join->on('products.id', '=', 'sold_products.product_id'))
                ->where('products.is_active', 1)
                ->select('products.*', DB::raw('COALESCE(sold_products.sold_qty, 0) as sold_qty'))
                ->orderByDesc('sold_qty');
        } else {
            $query = Products::query()->where('is_active', 1);
        }

        // Search
        if (trim($this->search) !== '') {
            $query->search($this->search);
        }

        // Category filter
        if (! empty($this->selectedCategories)) {
            $categoryIdsForFilter = $this->expandCategoryFilterIds($this->selectedCategories, $childrenByParent);
            $query->whereIn('category_id', $categoryIdsForFilter);
        }

        // Price range filter
        if ($this->priceRange !== 'all' && isset($priceRangeOptions[$this->priceRange])) {
            $min = (float) $priceRangeOptions[$this->priceRange]['min'];
            $max = (float) $priceRangeOptions[$this->priceRange]['max'];
            $query->whereBetween('base_price', [$min, $max]);
        }

        if (! empty($this->sizes)) {
            $normalizedSizes = array_values(array_unique(array_map(
                fn ($size) => strtoupper(trim((string) $size)),
                $this->sizes
            )));

            $query->whereHas('skus', function ($builder) use ($normalizedSizes): void {
                $builder->whereIn('size', $normalizedSizes)
                    ->where('stock', '>', 0);
            });
        }

        // Sorting (bỏ qua nếu filter best-seller đã tự sắp xếp)
        if ($this->filter !== 'best-seller') {
            match ($this->sort) {
                'newest' => $query->latest('created_at'),
                'name-asc' => $query->orderBy('name'),
                'price-asc' => $query->orderBy('base_price'),
                'price-desc' => $query->orderByDesc('base_price'),
                'popular' => $query->orderByDesc('id'),
                default => $query->latest('created_at'),
            };
        }

        $products = $query
            ->with(['category:id,name', 'collection:id,name'])
            ->paginate(12);

        $products->getCollection()->transform(fn (Products $product) => FlashSalePricing::applyProduct($product));

        $sizePriority = ['XS' => 1, 'S' => 2, 'M' => 3, 'L' => 4, 'XL' => 5, 'XXL' => 6, 'XXXL' => 7];

        $sizeOptions = ProductSkus::query()
            ->select('size')
            ->whereNotNull('size')
            ->where('size', '!=', '')
            ->where('stock', '>', 0)
            ->whereHas('product', function ($builder): void {
                $builder->where('is_active', 1);
            })
            ->distinct()
            ->pluck('size')
            ->map(fn ($size) => strtoupper(trim((string) $size)))
            ->filter(fn ($size) => $size !== '')
            ->unique()
            ->sortBy(fn ($size) => $sizePriority[$size] ?? (100 + strlen($size)))
            ->values()
            ->all();

        return view('livewire.user.product-listing', [
            'products' => $products,
            'categoryOptions' => $categoryOptions,
            'categoryTree' => $categoryTree,
            'sortOptions' => $sortOptions,
            'priceRangeOptions' => $priceRangeOptions,
            'sizeOptions' => $sizeOptions,
        ]);
    }

    private function getSortOptions(): array
    {
        return [
            'newest' => 'Mặc định',
            'name-asc' => 'Tên A-Z',
            'price-asc' => 'Giá tăng dần',
            'price-desc' => 'Giá giảm dần',
            'popular' => 'Phổ biến nhất',
        ];
    }

    private function getPriceRangeOptions(): array
    {
        return [
            'under-500' => ['label' => 'Dưới 500.000đ', 'min' => 0, 'max' => 500000],
            '500-1000' => ['label' => '500.000đ - 1.000.000đ', 'min' => 500000, 'max' => 1000000],
            '1000-2000' => ['label' => '1.000.000đ - 2.000.000đ', 'min' => 1000000, 'max' => 2000000],
            'above-2000' => ['label' => 'Trên 2.000.000đ', 'min' => 2000000, 'max' => PHP_INT_MAX],
        ];
    }

    private function expandCategoryFilterIds(array $selectedIds, Collection $childrenByParent): array
    {
        $queue = array_values(array_unique(array_map('intval', $selectedIds)));
        $expanded = [];

        while (! empty($queue)) {
            $id = (int) array_pop($queue);

            if (isset($expanded[$id])) {
                continue;
            }

            $expanded[$id] = true;
            $children = $childrenByParent->get($id, collect());

            foreach ($children as $child) {
                $childId = (int) $child->id;

                if (! isset($expanded[$childId])) {
                    $queue[] = $childId;
                }
            }
        }

        return array_keys($expanded);
    }

    private function applyCategoryFilterFromQuery(): void
    {
        if ($this->category === '' || ! empty($this->selectedCategories)) {
            return;
        }

        $categoryId = Categories::query()
            ->where('slug', $this->category)
            ->where('is_active', 1)
            ->value('id');

        if ($categoryId === null) {
            return;
        }

        $this->selectedCategories = [(int) $categoryId];
    }

    public function saveVoucher(int $voucherId): void
    {
        if (! Auth::check()) {
            return;
        }

        $voucherService = new VoucherService;
        $userId = (int) Auth::id();

        $voucher = Voucher::query()->find($voucherId);

        if (! $voucher) {
            $this->dispatch('app-toast', message: 'Không tìm thấy voucher.', type: 'error');

            return;
        }

        // Kiểm tra voucher có khả dụng không
        if (! $voucherService->isVoucherAvailable($voucher)) {
            $this->dispatch('app-toast', message: 'Voucher hiện không khả dụng.', type: 'error');

            return;
        }

        // Kiểm tra voucher đã được sử dụng bởi user này chưa
        if ($voucherService->isVoucherUsedByUser($userId, $voucherId)) {
            $this->dispatch('app-toast', message: 'Voucher này đã được sử dụng, không thể sử dụng lại.', type: 'error');

            return;
        }

        // Xóa voucher hết hạn và đã dùng
        $voucherService->cleanupExpiredAndUsedVouchers($userId);

        // Kiểm tra xem user đã lưu voucher này chưa
        $existingUserVoucher = UserVoucher::query()
            ->where('user_id', $userId)
            ->where('voucher_id', $voucherId)
            ->first();

        if ($existingUserVoucher && $existingUserVoucher->status === VoucherStatus::UNUSED->value) {
            $this->dispatch('app-toast', message: 'Voucher đã có trong ví của bạn.', type: 'success');

            return;
        }

        // Nếu đã sử dụng trước đó, không cho phép thêm lại
        if ($existingUserVoucher && $existingUserVoucher->status === VoucherStatus::USED->value) {
            $this->dispatch('app-toast', message: 'Voucher này đã được sử dụng, không thể sử dụng lại.', type: 'error');

            return;
        }

        $userVoucher = UserVoucher::query()->create([
            'user_id' => $userId,
            'voucher_id' => $voucherId,
            'status' => VoucherStatus::UNUSED->value,
            'collected_at' => now(),
        ]);

        $voucherCount = UserVoucher::query()
            ->where('user_id', $userId)
            ->where('status', VoucherStatus::UNUSED->value)
            ->count();

        $this->dispatch('voucher-count-updated', count: $voucherCount);
        $this->dispatch('app-toast', message: 'Đã lưu voucher vào tài khoản của bạn.', type: 'success');
    }
}
