<?php

namespace App\Rules;

use App\Repositories\SettingRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class SettingValidatorRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $settingRepository = app(SettingRepository::class);

        foreach ($value as $key => $settingValue) {
            $setting = $settingRepository->findById($key);

            $rules = match ($setting->name) {
                'logo' => ['required', 'file', 'mimes:png,svg,jpeg,jpg'],

                'android_version' => ['required', 'string', 'regex:/^\d+\.\d+\.\d+$/',
                    function ($attribute, $versionInput, $fail) use ($setting) {
                        $currentVersion = $setting->value;
                        if (version_compare($versionInput, $currentVersion, '<')) {
                            return $fail("The version must be newer than: $currentVersion.");
                        }
                    },
                ],
                'ios_version' => ['required', 'string', 'regex:/^\d+\.\d+\.\d+$/',
                    function ($attribute, $versionInput, $fail) use ($setting) {
                        $currentVersion = $setting->value;
                        if (version_compare($versionInput, $currentVersion, '<')) {
                            return $fail("The version must be newer than: $currentVersion.");
                        }
                    },
                ],

                default => [],
            };

            // Only validate if there are rules
            if (! empty($rules)) {
                $validator = Validator::make(
                    ['value' => $settingValue['value']],
                    ['value' => $rules]
                );

                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $error) {
                        $fail("$setting->name : $error");
                    }
                }
            }
        }
    }
}
