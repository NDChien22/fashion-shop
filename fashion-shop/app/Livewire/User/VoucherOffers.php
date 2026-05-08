<?php

namespace App\Livewire\User;

use App\Models\UserVoucher;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VoucherOffers extends Component
{
    /**
     * @var array<int>
     */
    public array $savedVoucherIds = [];

    public function mount(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->savedVoucherIds = UserVoucher::query()
            ->where('user_id', Auth::id())
            ->where('status', 'unused')
            ->pluck('voucher_id')
            ->map(fn ($voucherId) => (int) $voucherId)
            ->all();
    }

    public function saveVoucher(int $voucherId): void
    {
        if (! Auth::check()) {
            return;
        }

        $voucher = Voucher::query()->find($voucherId);

        if (! $voucher) {
            $this->dispatch('app-toast', message: 'Không tìm thấy voucher.', type: 'error');

            return;
        }

        if (! $this->isVoucherAvailable($voucher)) {
            $this->dispatch('app-toast', message: 'Voucher hiện không khả dụng.', type: 'error');

            return;
        }

        if (in_array($voucherId, $this->savedVoucherIds, true)) {
            $this->dispatch('voucher-count-updated', count: count($this->savedVoucherIds));
            $this->dispatch('app-toast', message: 'Voucher đã có trong ví của bạn.', type: 'success');

            return;
        }

        $userVoucher = UserVoucher::query()->firstOrCreate(
            [
                'user_id' => Auth::id(),
                'voucher_id' => $voucher->id,
            ],
            [
                'status' => 'unused',
                'collected_at' => now(),
            ]
        );

        $this->savedVoucherIds = UserVoucher::query()
            ->where('user_id', Auth::id())
            ->where('status', 'unused')
            ->pluck('voucher_id')
            ->map(fn ($savedVoucherId) => (int) $savedVoucherId)
            ->all();

        $this->dispatch('voucher-count-updated', count: count($this->savedVoucherIds));

        if ($userVoucher->wasRecentlyCreated) {
            $this->dispatch('app-toast', message: 'Đã lưu voucher vào tài khoản của bạn.', type: 'success');

            return;
        }

        $this->dispatch('app-toast', message: 'Voucher đã có trong ví của bạn.', type: 'success');
    }

    public function render()
    {
        try {
            $now = now();
            $userId = Auth::id();

            $vouchers = Voucher::query()
                ->with(['categoryDetail:id,name'])
                ->where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where(function ($query) {
                    $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
                })
                ->when(Auth::check(), function ($query) use ($userId): void {
                    $query->whereDoesntHave('userVouchers', function ($voucherQuery) use ($userId): void {
                        $voucherQuery->where('user_id', $userId);
                    });
                })
                ->orderByDesc('discount_value')
                ->orderByDesc('id')
                ->limit(8)
                ->get();

            return view('livewire.user.voucher-offers', [
                'vouchers' => $vouchers,
            ]);
        } catch (\Exception $e) {
            Log::error('VoucherOffers render error: '.$e->getMessage());

            return view('livewire.user.voucher-offers', [
                'vouchers' => collect(),
            ]);
        }
    }

    private function isVoucherAvailable(Voucher $voucher): bool
    {
        if (! $voucher->is_active) {
            return false;
        }

        if ($voucher->start_date && now()->lt($voucher->start_date)) {
            return false;
        }

        if ($voucher->end_date && now()->gt($voucher->end_date)) {
            return false;
        }

        if (! is_null($voucher->usage_limit) && (int) $voucher->used_count >= (int) $voucher->usage_limit) {
            return false;
        }

        return true;
    }
}
