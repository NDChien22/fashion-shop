<?php

namespace App\Livewire\User;

use App\Models\Collections;
use App\Models\Products;
use App\Support\FlashSalePricing;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CollectionListing extends Component
{
    use WithPagination;

    public Collections $collection;

    #[Url]
    public string $sort = 'newest';

    #[Url]
    public string $priceRange = 'all';

    #[Url]
    public array $selectedSizes = [];

    public function mount(Collections $collection)
    {
        $this->collection = $collection;
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function updatingPriceRange()
    {
        $this->resetPage();
    }

    public function updatingSelectedSizes()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->sort = 'newest';
        $this->priceRange = 'all';
        $this->selectedSizes = [];
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->collection->products()
            ->where('is_active', 1);

        // Price range filter
        $priceRanges = [
            'under-500' => [0, 500000],
            '500-1000' => [500000, 1000000],
            '1000-2000' => [1000000, 2000000],
            'above-2000' => [2000000, PHP_INT_MAX],
        ];

        if ($this->priceRange !== 'all' && isset($priceRanges[$this->priceRange])) {
            [$min, $max] = $priceRanges[$this->priceRange];
            $query->whereBetween('base_price', [$min, $max]);
        }

        // Size filter - filter through SKU relationship
        if (! empty($this->selectedSizes)) {
            $query->whereHas('skus', function ($q) {
                $q->whereIn('size', $this->selectedSizes);
            });
        }

        // Sorting
        match ($this->sort) {
            'newest' => $query->latest('created_at'),
            'price-asc' => $query->orderBy('base_price'),
            'price-desc' => $query->orderByDesc('base_price'),
            'popular' => $query->orderByDesc('view_count'),
            default => $query->latest('created_at'),
        };

        $products = $query
            ->with(['category:id,name', 'collection:id,name', 'reviews'])
            ->paginate(12);

        $products->getCollection()->transform(fn (Products $product) => FlashSalePricing::applyProduct($product));

        return view('livewire.user.collection-listing', [
            'products' => $products,
            'collection' => $this->collection,
        ]);
    }
}
