<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\Request;
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
}
