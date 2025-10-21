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
}
