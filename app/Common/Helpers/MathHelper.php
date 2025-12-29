<?php

namespace App\Common\Helpers;

class MathHelper
{
    public static function formatDecimal($number, $decimal = 0): string
    {
        return number_format($number, $decimal, '.', '');
    }

    public static function stringToNumber($numberString): float
    {
        $numberString = str_replace(',', '.', $numberString);
        return floatval($numberString);
    }
}
