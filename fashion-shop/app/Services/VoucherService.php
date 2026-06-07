<?php

namespace App\Services;

use App\Enums\VoucherStatus;
use App\Models\UserVoucher;
use App\Models\Voucher;

class VoucherService
{
    /**
     * Xóa voucher hết hạn và đã dùng khỏi ví người dùng
     */
    public function cleanupExpiredAndUsedVouchers(int $userId): void
    {
        // Không xoá những voucher đã dùng để tránh cho người dùng lưu lại voucher đã sử
        // dụng một lần nữa. Chỉ cập nhật trạng thái hết hạn và xoá các bản ghi hết hạn.

        // Cập nhật trạng thái voucher hết hạn
        UserVoucher::query()
            ->where('user_id', $userId)
            ->where('status', VoucherStatus::UNUSED->value)
            ->whereHas('voucher', function ($query) {
                $query->where(function ($q) {
                    $q->where('end_date', '<', now())
                        ->orWhere('is_active', false);
                });
            })
            ->update(['status' => VoucherStatus::EXPIRED->value]);

        // Xóa những voucher hết hạn
        UserVoucher::query()
            ->where('user_id', $userId)
            ->where('status', VoucherStatus::EXPIRED->value)
            ->delete();
    }

    /**
     * Kiểm tra xem voucher có khả dụng không
     */
    public function isVoucherAvailable(Voucher $voucher): bool
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

    /**
     * Kiểm tra voucher đã được dùng bởi user chưa
     */
    public function isVoucherUsedByUser(int $userId, int $voucherId): bool
    {
        return UserVoucher::query()
            ->where('user_id', $userId)
            ->where('voucher_id', $voucherId)
            ->where('status', VoucherStatus::USED->value)
            ->exists();
    }

    /**
     * Lấy danh sách voucher khả dụng của user (không hết hạn, không dùng)
     */
    public function getAvailableVouchersForUser(int $userId)
    {
        // Xóa voucher hết hạn trước
        $this->cleanupExpiredAndUsedVouchers($userId);

        return UserVoucher::query()
            ->with('voucher')
            ->where('user_id', $userId)
            ->where('status', VoucherStatus::UNUSED->value)
            ->whereHas('voucher', function ($query) {
                $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where(function ($scope) {
                        $scope->whereNull('usage_limit')
                            ->orWhereColumn('used_count', '<', 'usage_limit');
                    });
            })
            ->orderByDesc('id')
            ->get();
    }
}
