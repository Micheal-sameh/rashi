<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    public function index()
    {
        $settings = $this->settingService->index();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // dd($request->files);

        $this->settingService->update($request->settings, $request->allFiles()['settings']);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
