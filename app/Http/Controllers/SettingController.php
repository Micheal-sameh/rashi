<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    public function index()
    {
        $settings = $this->settingService->index();

        return view('settings.index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request)
    {

        $this->settingService->update($request->settings, $request?->allFiles()['settings'] ?? null);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
