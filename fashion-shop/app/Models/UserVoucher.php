<?php

namespace App\Models;

use App\Enums\VoucherStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVoucher extends Model
{
    protected $fillable = [
        'user_id',
        'voucher_id',
        'status',
        'collected_at',
        'used_at',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function scopeUnused(Builder $query): Builder
    {
        return $query->where('status', VoucherStatus::UNUSED->value);
    }

    public function scopeUsed(Builder $query): Builder
    {
        return $query->where('status', VoucherStatus::USED->value);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', VoucherStatus::EXPIRED->value);
    }

    public function isExpired(): bool
    {
        $voucher = $this->voucher;
        if (! $voucher) {
            return true;
        }

        return ! $voucher->is_active
            || ($voucher->end_date && now()->gt($voucher->end_date));
    }

    public function isUsed(): bool
    {
        return $this->status === VoucherStatus::USED->value;
    }
}
