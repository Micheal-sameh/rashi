<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(Request $request)
    {
        $users = $this->userService->index();
        if ($request->ajax()) {
            return view('partials.user-table', compact('users'))->render();
        }
        return view('users.index', compact('users'));
    }

    public function show() {}
}
