<?php

namespace App\Livewire\Admin;

use App\Models\FlashSale as FlashSaleModel;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class FlashSaleManager extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $statusFilter): void
    {
        $this->statusFilter = $statusFilter;
        $this->resetPage();
    }

    public function toggleStatus(int $flashSaleId): void
    {
        $flashSale = FlashSaleModel::query()->find($flashSaleId);

        if (! $flashSale) {
            session()->flash('error', 'Không tìm thấy flash sale cần cập nhật trạng thái.');
            return;
        }

        if ($flashSale->end_date && Carbon::parse($flashSale->end_date)->lt(now())) {
            session()->flash('error', 'Flash sale đã hết hạn nên không thể chỉnh trạng thái.');
            return;
        }

        $flashSale->update([
            'is_active' => ! (bool) $flashSale->is_active,
        ]);

        session()->flash('success', 'Đã cập nhật trạng thái flash sale.');
    }

    public function render()
    {
        $now = now();

        $query = FlashSaleModel::query()
            ->with([
                'categoryDetail:id,name',
                'collectionDetail:id,name',
                'productDetail:id,name,product_code',
            ])
            ->when($this->statusFilter !== 'all', function ($builder) use ($now): void {
                if ($this->statusFilter === 'active') {
                    $builder->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                }

                if ($this->statusFilter === 'upcoming') {
                    $builder->where('is_active', true)
                        ->where('start_date', '>', $now);
                }

                if ($this->statusFilter === 'paused') {
                    $builder->where('is_active', false)
                        ->where('end_date', '>=', $now);
                }

                if ($this->statusFilter === 'expired') {
                    $builder->where('end_date', '<', $now);
                }
            });

        $flashSales = $query->latest('id')->paginate(9);

        $stats = [
            'total' => FlashSaleModel::query()->count(),
            'active' => FlashSaleModel::query()
                ->where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->count(),
            'upcoming' => FlashSaleModel::query()
                ->where('is_active', true)
                ->where('start_date', '>', $now)
                ->count(),
            'expired' => FlashSaleModel::query()
                ->where('end_date', '<', $now)
                ->count(),
            'used' => FlashSaleModel::query()->sum('used_count'),
        ];

        return view('livewire.admin.flash-sale-manager', [
            'flashSales' => $flashSales,
            'stats' => $stats,
        ]);
    }
}
