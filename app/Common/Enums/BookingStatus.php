<?php

namespace App\Common\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case AVAILABLE = 'available';
    case OCCUPIED = 'occupied';

    public static function values(): array
    {
        return array_map(fn(self $c) => $c->value, self::cases());
    }
}
