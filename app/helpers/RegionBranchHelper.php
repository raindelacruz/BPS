<?php

namespace App\Helpers;

class RegionBranchHelper
{
    public static function mapping(): array
    {
        return [
            'Central Office' => [
                'Administrative and General Services Department',
            ],
            'Region I' => [
                'Eastern Pangasinan Branch',
                'Ilocos Norte Branch',
                'La Union Branch',
                'Region I',
            ],
            'Region II' => [
                'Region II',
                'Cagayan Branch',
                'Isabela Branch',
                'Nueva Vizcaya Branch',
            ],
            'Region III' => [
                'Region III',
                'Tarlac Branch',
                'Bulacan Branch',
                'Pampanga Branch',
                'Nueva Ecija Branch',
            ],
            'Region IV' => [
                'Oriental Mindoro Branch',
                'Quezon Branch',
                'Laguna Branch',
                'Region IV',
                'Batangas Branch',
                'Occidental Mindoro Branch',
                'Palawan Branch',
            ],
            'Region V' => [
                'Region V',
                'Albay Branch',
                'Camarines Sur Branch',
                'Sorsogon Branch',
            ],
            'Region VI' => [
                'Negros Occidental Branch',
                'Capiz Branch',
                'Iloilo Branch',
                'Region VI',
            ],
            'Region VII' => [
                'Cebu Branch',
                'Region VII',
                'Bohol Branch',
                'Negros Oriental Branch',
            ],
            'Region VIII' => [
                'Region VIII',
                'Leyte Branch',
                'Samar Branch',
            ],
            'Region IX' => [
                'Region IX',
                'Zamboanga Branch',
                'Zamboanga del Sur Branch',
            ],
            'Region X' => [
                'Bukidnon Branch',
                'Misamis Oriental Branch',
                'Lanao del Norte Branch',
                'Region X',
            ],
            'Region XI' => [
                'Region XI',
                'Davao Del Sur Branch',
                'Davao Oriental Branch',
                'Davao del Norte',
            ],
            'Region XII' => [
                'South Cotabato',
                'Sultan Kudarat',
                'North Cotabato',
                'Region XII',
            ],
            'NCR' => [
                'Central District',
                'East District',
                'NCR',
            ],
            'ARMM' => [
                'ARMM',
                'Maguindanao Branch',
                'Lanao del Sur Branch',
                'BASULTA Branch',
            ],
            'CARAGA' => [
                'CARAGA',
                'Agusan Del Sur Branch',
                'Surigao Del Sur Branch',
            ],
        ];
    }

    public static function regions(): array
    {
        return array_keys(self::mapping());
    }

    public static function branchesForRegion(string $region): array
    {
        return self::mapping()[$region] ?? [];
    }

    public static function isValidRegion(string $region): bool
    {
        return array_key_exists($region, self::mapping());
    }

    public static function branchBelongsToRegion(string $region, string $branch): bool
    {
        return in_array($branch, self::branchesForRegion($region), true);
    }
}
