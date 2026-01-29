<?php

namespace App\Enums;

use Illuminate\Support\Facades\App;
use InvalidArgumentException;

class BonusPenaltyStatus
{
    public const PENDING_APPROVAL = 1;

    public const APPLIED = 2;

    public const REJECTED = 3;

    private static array $translations = [
        self::PENDING_APPROVAL => [
            'en' => 'Pending Approval',
            'ar' => 'في انتظار الموافقة',
        ],
        self::APPLIED => [
            'en' => 'Applied',
            'ar' => 'تم التطبيق',
        ],
        self::REJECTED => [
            'en' => 'Rejected',
            'ar' => 'مرفوض',
        ],
    ];

    public static function all(): array
    {
        $locale = App::isLocale('en') ? 'en' : 'ar';

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
            throw new InvalidArgumentException("Invalid bonus/penalty status value: {$value}");
        }

        return self::$translations[$value][App::isLocale('en') ? 'en' : 'ar'];
    }

    public static function getValues(): array
    {
        return array_keys(self::$translations);
    }
}
