<?php

namespace App\Common\Enums;

enum UserType: string
{
    case TENANT = 'tenant';
    case LANDLORD = 'landlord';

    public static function values(): array
    {
        return array_map(fn(self $c) => $c->value, self::cases());
    }
}
