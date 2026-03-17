<?php

namespace App\Common\Enums;

enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case VIEWING_SCHEDULED = 'viewing_scheduled';
    case VIEWED = 'viewed';
    case NEGOTIATING = 'negotiating';
    case WAITING_DECISION = 'waiting_decision';
    case WON = 'won';
    case LOST = 'lost';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
