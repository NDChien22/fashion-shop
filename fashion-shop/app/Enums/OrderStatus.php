<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case RETURNED = 'returned';
    case EXCHANGED = 'exchanged';
    case CANCELLED = 'cancelled';
    case PAYMENT_FAILED = 'payment_failed';

    // map giá trị trạng thái đơn hàng
    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }

    // hiển thị cho từng trạng thái đơn hàng
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ xử lý',
            self::PROCESSING => 'Đang xử lý',
            self::COMPLETED => 'Hoàn thành',
            self::RETURNED => 'Đã trả hàng',
            self::EXCHANGED => 'Đã đổi hàng',
            self::CANCELLED => 'Đã hủy',
            self::PAYMENT_FAILED => 'Thanh toán thất bại',
        };
    }

    // CSS badge với từng trạng thái
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-700 border-amber-200',
            self::PROCESSING => 'bg-blue-100 text-blue-700 border-blue-200',
            self::COMPLETED => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::RETURNED => 'bg-orange-100 text-orange-700 border-orange-200',
            self::EXCHANGED => 'bg-cyan-100 text-cyan-700 border-cyan-200',
            self::CANCELLED => 'bg-red-100 text-red-700 border-red-200',
            self::PAYMENT_FAILED => 'bg-red-100 text-red-700 border-red-200',
        };
    }
}
