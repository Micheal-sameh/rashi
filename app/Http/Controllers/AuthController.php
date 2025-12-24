<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ArAuthentication;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ArAuthentication;

    public function __construct(protected UserService $userService) {}

    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(AdminLoginRequest $request)
    {
        $user = $this->arLogin($request->membership_code, $request->password);
        if (! $user instanceof User) {
            return $user;
        }
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('competitions.index'))->with('success', 'Welcome back, Admin!');

        // return redirect()->back()
        //     ->withInput($request->only('membership_code', 'remember'))
        //     ->withErrors(['membership_code' => __('auth.failed')]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('loginPage');
    }
}
