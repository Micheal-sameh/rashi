<?php

namespace App\Enums;

use Illuminate\Support\Facades\App;
use InvalidArgumentException;

class AppearanceStatus
{
    public const HIDDEN = 0;

    public const APPEAR = 1;

    private static array $translations = [
        self::HIDDEN => [
            'en' => 'Hidden',
            'ar' => 'مخفي',
        ],
        self::APPEAR => [
            'en' => 'Appear',
            'ar' => 'ظاهر',
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
            throw new InvalidArgumentException("Invalid appearance status value: {$value}");
        }

        return self::$translations[$value][App::isLocale('ar') ? 'ar' : 'en'];
    }

    public static function getValues(): array
    {
        return array_keys(self::$translations);
    }
}
