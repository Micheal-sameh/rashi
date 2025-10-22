<?php

namespace App\Http\Controllers;

use App\DTOs\UsersFilterDTO;
use App\Http\Requests\UpdateUserGroupRequest;
use App\Http\Resources\UserResource;
use App\Repositories\GroupRepository;
use App\Repositories\PointHistoryRepository;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected GroupRepository $groupRepository,
        protected PointHistoryRepository $pointHistoryRepository,
    ) {}

    public function index(Request $request)
    {
        $input = new UsersFilterDTO(...$request->only(
            'name', 'group_id', 'sort_by', 'direction'
        ));
        $users = $this->userService->index($input);
        $groups = $this->groupRepository->dropdown();
        // $users = UserResource::collection($this->userService->index($input));
        if ($request->is_filter) {
            return view('users.user-table', compact('users'))->render();
        }

        return view('users.index', compact('users', 'groups'));
    }

    public function show($id)
    {
        $user = $this->userService->show($id);
        $points = $this->pointHistoryRepository->userHistory($id);
        $points->load('user', 'subject');
        $groups = $this->groupRepository->dropdown();

        return view('users.show', compact('user', 'points', 'groups'));
    }

    public function updateGroups(UpdateUserGroupRequest $request, $id)
    {
        $user = $this->userService->updateGroups($request->groups, $id);

        $groupNames = $user->groups->pluck('name')->join(', ') ?: __('messages.not_assigned');

        return redirect()->back()->with('message', 'sucess updated');
    }

    public function leaderboard()
    {
        $users = $this->userService->leaderboard();

        return view('users.leaderboard', compact('users'));
    }
}
