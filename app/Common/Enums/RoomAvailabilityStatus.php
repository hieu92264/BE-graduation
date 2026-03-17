<?php

namespace App\Common\Enums;

enum RoomAvailabilityStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case OCCUPIED = 'occupied';
    case HIDDEN = 'hidden';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
