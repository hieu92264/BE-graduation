<?php

namespace App\Common\Enums;

enum DealStatus: string
{
    case DRAFT = 'draft';
    case RESERVED = 'reserved';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
