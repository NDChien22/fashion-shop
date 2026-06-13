<?php

namespace App\Livewire\User;

use App\Models\Products;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ProductSearch extends Component
{
    public string $search = '';

    public bool $showResults = false;

    public function updatedSearch(): void
    {
        $this->showResults = mb_strlen(trim($this->search)) >= 2;
    }

    public function submitSearch(): void
    {
        $keyword = trim($this->search);

        if ($keyword === '') {
            $this->showResults = false;

            $this->redirect(route('user.product'));

            return;
        }

        $this->redirect(route('user.product', ['search' => $keyword]));
    }

    public function selectSuggestion(string $slug): void
    {
        $this->redirect(route('user.product-detail', ['product' => $slug]));
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
            ->search($keyword)
            ->orderByDesc('id')
            ->limit(6)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.user.product-search', [
            'products' => $this->products,
        ]);
    }
}
