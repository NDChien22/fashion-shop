<?php

namespace App\Enums;

enum OrderReturnRequestStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';

    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ xử lý',
            self::APPROVED => 'Đã duyệt',
            self::REJECTED => 'Đã từ chối',
            self::COMPLETED => 'Đã hoàn tất',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-700 border-amber-200',
            self::APPROVED => 'bg-blue-100 text-blue-700 border-blue-200',
            self::REJECTED => 'bg-rose-100 text-rose-700 border-rose-200',
            self::COMPLETED => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        };
    }
}
