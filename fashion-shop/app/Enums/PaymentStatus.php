<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    // map giá trị trạng thái thanh toán
    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }

    // hiển thị cho từng trạng thái thanh toán.
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ thanh toán',
            self::PAID => 'Đã thanh toán',
            self::REFUNDED => 'Đã hoàn tiền',
            self::FAILED => 'Thanh toán thất bại',
        };
    }

    // CSS badge với từng trạng thái.
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-700 border-amber-200',
            self::PAID => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::REFUNDED => 'bg-violet-100 text-violet-700 border-violet-200',
            self::FAILED => 'bg-red-100 text-red-700 border-red-200',
        };
    }
}
