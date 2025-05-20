<?php

namespace App\Services;

use App\Repositories\SettingRepository;

class SettingService
{
    public function __construct(protected SettingRepository $settingRepository) {}

    public function index()
    {
        $settings = $this->settingRepository->index();

        return $settings;
    }

    public function update($settings, $files)
    {
        return $this->settingRepository->update($settings, $files);
    }
}
