<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;

class SettingController extends BaseController
{
    public function getVersions()
    {
        $iosVersion = Setting::where('name', 'ios_version')->first()?->value ?? '1.0.0';
        $androidVersion = Setting::where('name', 'android_version')->first()?->value ?? '1.0.0';

        return $this->apiResponse([
            'ios_version' => $iosVersion,
            'android_version' => $androidVersion,
        ]);
    }

    public function aboutUs()
    {
        $aboutUs = Setting::where('name', 'about_us')->first();

        return $this->apiResponse($aboutUs?->value);
    }

    public function terms()
    {
        $terms = Setting::where('name', 'terms')->first();

        return $this->apiResponse($terms?->value);
    }
}
