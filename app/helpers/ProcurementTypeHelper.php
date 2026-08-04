<?php

namespace App\Helpers;

class ProcurementTypeHelper
{
    public const TYPES = [
        'competitive_bidding' => 'Competitive Bidding',
        'svp' => 'Small Value Procurement',
    ];

    public static function all(): array
    {
        return self::TYPES;
    }

    public static function values(): array
    {
        return array_keys(self::TYPES);
    }

    public static function label(string $value): string
    {
        return self::TYPES[$value] ?? $value;
    }
}
