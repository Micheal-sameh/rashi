<?php

namespace App\Http\Controllers;

use App\DTOs\UsersFilterDTO;
use App\Repositories\GroupRepository;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected GroupRepository $groupRepository,
    ) {}

    public function index(Request $request)
    {
        $input = new UsersFilterDTO(...$request->only(
            'name', 'group_id', 'sort_by', 'direction'
        ));
        $users = $this->userService->index($input);
        $groups = $this->groupRepository->dropdown();

        if ($request->is_filter) {
            return view('users.user-table', compact('users'))->render();
        }

        return view('users.index', compact('users', 'groups'));
    }

    public function show() {}
}
