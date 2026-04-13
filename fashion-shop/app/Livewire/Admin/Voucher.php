<?php

namespace App\Livewire\Admin;

use App\Models\Voucher as VoucherModel;
use Livewire\Component;
use Livewire\WithPagination;

class Voucher extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $scopeFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'scopeFilter' => ['except' => 'all'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingScopeFilter(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $voucherId): void
    {
        $voucher = VoucherModel::query()->find($voucherId);

        if (! $voucher) {
            session()->flash('error', 'Không tìm thấy voucher cần cập nhật trạng thái.');
            return;
        }

        $voucher->update([
            'is_active' => ! (bool) $voucher->is_active,
        ]);

        session()->flash('success', 'Đã cập nhật trạng thái voucher.');
    }

    public function deleteVoucher(int $voucherId): void
    {
        $voucher = VoucherModel::query()->find($voucherId);

        if (! $voucher) {
            session()->flash('error', 'Không tìm thấy voucher cần xóa.');
            return;
        }

        $voucher->delete();
        session()->flash('success', 'Đã xóa voucher.');
        $this->resetPage();
    }

    public function render()
    {
        $now = now();

        $query = VoucherModel::query()
            ->with([
                'categoryDetail:id,name',
                'collectionDetail:id,name',
                'productDetail:id,name,product_code',
            ])
            ->when(trim($this->search) !== '', function ($builder): void {
                $keyword = trim($this->search);

                $builder->where(function ($subQuery) use ($keyword): void {
                    $subQuery->where('code', 'like', '%' . $keyword . '%')
                        ->orWhere('category', 'like', '%' . $keyword . '%')
                        ->orWhere('discount_type', 'like', '%' . $keyword . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function ($builder) use ($now): void {
                if ($this->statusFilter === 'active') {
                    $builder->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                }

                if ($this->statusFilter === 'inactive') {
                    $builder->where('is_active', false);
                }

                if ($this->statusFilter === 'expired') {
                    $builder->where('end_date', '<', $now);
                }
            })
            ->when($this->scopeFilter !== 'all', function ($builder): void {
                if ($this->scopeFilter === 'all_products') {
                    $builder->where('category', 'all');
                    return;
                }

                $builder->where('category', $this->scopeFilter);
            });

        $vouchers = $query
            ->latest('id')
            ->paginate(9);

        $stats = [
            'total' => VoucherModel::query()->count(),
            'active' => VoucherModel::query()
                ->where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->count(),
            'expiringSoon' => VoucherModel::query()
                ->where('is_active', true)
                ->where('end_date', '>=', $now)
                ->where('end_date', '<=', $now->copy()->addDays(7))
                ->count(),
            'used' => VoucherModel::query()->sum('used_count'),
        ];

        return view('livewire.admin.voucher', [
            'vouchers' => $vouchers,
            'stats' => $stats,
        ]);
    }
}
