<?php

namespace App\Enums;

enum VoucherStatus: string
{
    case UNUSED = 'unused';
    case USED = 'used';
    case EXPIRED = 'expired';

    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::UNUSED => 'Chưa sử dụng',
            self::USED => 'Đã sử dụng',
            self::EXPIRED => 'Hết hạn',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::UNUSED => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::USED => 'bg-slate-100 text-slate-700 border-slate-200',
            self::EXPIRED => 'bg-red-100 text-red-700 border-red-200',
        };
    }
}
