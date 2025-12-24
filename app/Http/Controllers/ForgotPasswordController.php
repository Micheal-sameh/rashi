<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Traits\ArAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use ArAuthentication;

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending the reset link email.
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Custom logic: Log the password reset request
        Log::info('Password reset link requested', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);
        $response = $this->resetPassword($request->email);

        return $response->successful()
            ? to_route('loginPage')->with('status', 'We have emailed your password reset link!')
            : back()->withErrors(['email' => 'We could not send the reset link. Please try again.']);
    }

    /**
     * Display the password reset form.
     */
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }
}
