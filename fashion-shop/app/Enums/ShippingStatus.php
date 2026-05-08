<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case PENDING = 'pending';
    case SHIPPING = 'shipping';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chưa giao',
            self::SHIPPING => 'Đang giao',
            self::DELIVERED => 'Đã giao',
            self::CANCELLED => 'Đã hủy giao',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-slate-100 text-slate-700 border-slate-200',
            self::SHIPPING => 'bg-cyan-100 text-cyan-700 border-cyan-200',
            self::DELIVERED => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::CANCELLED => 'bg-red-100 text-red-700 border-red-200',
        };
    }
}
