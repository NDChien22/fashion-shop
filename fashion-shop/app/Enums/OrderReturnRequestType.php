<?php

namespace App\Enums;

enum OrderReturnRequestType: string
{
    case RETURN = 'return';
    case EXCHANGE = 'exchange';

    public static function values(): array
    {
        return array_map(fn (self $type) => $type->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::RETURN => 'Trả hàng / hoàn tiền',
            self::EXCHANGE => 'Đổi hàng',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::RETURN => 'bg-violet-100 text-violet-700 border-violet-200',
            self::EXCHANGE => 'bg-cyan-100 text-cyan-700 border-cyan-200',
        };
    }
}
