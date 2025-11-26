<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

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
        Cache::forget('app_logo_url');

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function deleteAllTokens(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // Check if the provided password matches the authenticated user's password
        if (! Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->withErrors(['password' => 'The provided password is incorrect.']);
        }

        PersonalAccessToken::query()->delete();

        return redirect()->back()->with('success', 'All API tokens have been deleted. All users are now logged out.');
    }

    public function aboutUs()
    {
        $first = request()->segment(1);
        $aboutUs = $this->settingService->getSettingByName($first);

        return view('about_us.show', compact('aboutUs'));
    }

    public function editAboutUs()
    {
        $first = request()->segment(1);
        $aboutUs = $this->settingService->getSettingByName($first);

        return view('about_us.edit', compact('aboutUs'));
    }

    public function updateAboutUs(Request $request)
    {
        $first = request()->segment(1);
        $request->validate([
            'value' => 'required|string',
        ]);

        $aboutUsSetting = $this->settingService->getSettingByName($first);
        $aboutUsSetting->update([
            'value' => $request->value,
        ]);

        return redirect()->route('about_us.show')->with('success', 'About Us updated successfully.');
    }
}
