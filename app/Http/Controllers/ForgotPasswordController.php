<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
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
        \Log::info('Password reset link requested', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => 'We have emailed your password reset link!'])
            : back()->withErrors(['email' => 'We could not send the reset link. Please try again.']);
    }

    /**
     * Display the password reset form.
     */
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle password reset.
     */
    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                // Custom logic: Log successful password reset
                \Log::info('Password reset successful', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now(),
                ]);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('loginPage')->with('status', 'Your password has been reset successfully!')
            : back()->withErrors(['email' => 'The password reset link is invalid or has expired.']);
    }
}
