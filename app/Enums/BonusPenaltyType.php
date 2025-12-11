<?php

namespace App\Enums;

use Illuminate\Support\Facades\App;
use InvalidArgumentException;

class BonusPenaltyType
{
    public const BONUS = 1;

    public const PENALTY = 2;

    public const WELCOME_BONUS = 3;

    private static array $translations = [
        self::BONUS => [
            'en' => 'Bonus',
            'ar' => 'مكافأة',
        ],
        self::WELCOME_BONUS => [
            'en' => 'Welcome Bonus',
            'ar' => 'مكافأة ترحيبية',
        ],
        self::PENALTY => [
            'en' => 'Penalty',
            'ar' => 'عقوبة',
        ],
    ];

    public static function all(): array
    {
        $locale = App::isLocale('ar') ? 'ar' : 'en';

        return array_map(
            fn ($value) => [
                'name' => self::$translations[$value][$locale],
                'value' => $value,
            ],
            array_keys(self::$translations)
        );
    }

    public static function getStringValue(int $value): string
    {
        if (! isset(self::$translations[$value])) {
            throw new InvalidArgumentException("Invalid bonus/penalty type value: {$value}");
        }

        return self::$translations[$value][App::isLocale('ar') ? 'ar' : 'en'];
    }

    public static function getValues(): array
    {
        return array_keys(self::$translations);
    }
}
