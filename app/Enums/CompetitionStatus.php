<?php

namespace App\Enums;

use Illuminate\Support\Facades\App;
use InvalidArgumentException;

class CompetitionStatus
{
    public const PENDING = 1;
    public const ACTIVE = 2;
    public const FINISHED = 3;
    public const CANCELLED = 4;

    private static array $translations = [
        self::PENDING     => [
            'en' => 'Pending',
            'ar' => 'قيد الانتظار',
        ],
        self::ACTIVE    => [
            'en' => 'Active',
            'ar' => 'فعال',
        ],
        self::FINISHED   => [
            'en' => 'Finished',
            'ar' => 'منتهي',
        ],
        self::CANCELLED   => [
            'en' => 'Cancelled',
            'ar' => 'ملغى',
        ],
    ];

    public static function all(): array
    {
        $locale = App::isLocale('ar') ? 'ar' : 'en';

        return array_map(
            fn ($value) => [
                'name'  => self::$translations[$value][$locale],
                'value' => $value,
            ],
            array_keys(self::$translations)
        );
    }

    public static function getStringValue(int $value): string
    {
        if (!isset(self::$translations[$value])) {
            throw new InvalidArgumentException("Invalid listing type value: {$value}");
        }

        return self::$translations[$value][App::isLocale('ar') ? 'ar' : 'en'];
    }

    public static function getValues(): array
    {
        return array_keys(self::$translations);
    }
}
