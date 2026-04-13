<?php

namespace App\Livewire\User;

use App\Models\Products;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProductSearch extends Component
{
    public string $search = '';

    public bool $showResults = false;

    public function updatedSearch(): void
    {
        $this->showResults = mb_strlen(trim($this->search)) >= 2;
    }

    public function submitSearch()
    {
        $keyword = trim($this->search);

        if ($keyword === '') {
            $this->showResults = false;
            return;
        }

        return redirect()->route('user.product', ['search' => $keyword]);
    }

    public function selectSuggestion(string $keyword)
    {
        return redirect()->route('user.product', ['search' => $keyword]);
    }

    public function getProductsProperty(): Collection
    {
        $keyword = trim($this->search);

        if (mb_strlen($keyword) < 2) {
            return collect();
        }

        return Products::query()
            ->with(['category:id,name', 'collection:id,name'])
            ->where('is_active', true)
            ->where(function ($query) use ($keyword): void {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('product_code', 'like', '%' . $keyword . '%')
                    ->orWhereHas('category', function ($subQuery) use ($keyword): void {
                        $subQuery->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('collection', function ($subQuery) use ($keyword): void {
                        $subQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderByDesc('id')
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.user.product-search', [
            'products' => $this->products,
        ]);
    }
}