<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminQrLoginRequest;
use App\Models\User;
use App\Services\QrCodeCredentialsService;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->only('membership_code', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            if (! $user->hasRole(['admin'])) {
                Auth::logout();

                return redirect()->back()
                    ->withInput($request->only('membership_code', 'remember'))
                    ->withErrors(['membership_code' => __('messages.unauthorized')]);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('competitions.index'))->with('success', __('messages.welcome_back_admin'));
        }

        return redirect()->back()
            ->withInput($request->only('membership_code', 'remember'))
            ->withErrors(['membership_code' => __('auth.failed')]);
    }

    public function loginWithQr(AdminQrLoginRequest $request, QrCodeCredentialsService $qrCodeCredentialsService)
    {
        try {
            $credentials = $qrCodeCredentialsService->decode($request->qr_code);
        } catch (InvalidArgumentException) {
            return redirect()->back()->withErrors(['qr_code' => __('messages.invalid_qr_code')]);
        }

        $user = User::where('membership_code', $credentials['membership_code'])->first();

        if (! $user || ! $user->hasRole(['admin'])) {
            return redirect()->back()->withErrors(['qr_code' => __('messages.unauthorized')]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('competitions.index'))->with('success', __('messages.welcome_back_admin'));
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('loginPage');
    }
}
