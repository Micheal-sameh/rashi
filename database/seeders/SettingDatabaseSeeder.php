<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Setting::create([
            'name' => 'logo',
            'value' => 'null',
            'type' => 'file',
        ]);

        Setting::create([
            'name' => 'android_version',
            'value' => '0.0.0',
            'type' => 'string',
        ]);

        Setting::create([
            'name' => 'ios_version',
            'value' => '0.0.0',
            'type' => 'string',
        ]);
    }
}
