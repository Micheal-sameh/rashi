<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateProfilePicRequest;
use App\Http\Requests\LeaderBoardRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(protected UserService $userService) {}

    public function index()
    {
        $users = $this->userService->index();

        return $this->respondResource(UserResource::collection($users));
    }

    public function show($id)
    {
        $user = $this->userService->show($id);

        return $this->apiResponse(new UserResource($user));
    }

    public function me()
    {
        $user = $this->userService->show(auth()->id())->load([
            'roles:id,name',
            'media',
            'groups' => function ($q) {
                $q->where('group_id', '!=', 1);
            },
        ]);

        return $this->apiResponse(new UserResource($user));
    }

    public function profilePic(CreateProfilePicRequest $request)
    {
        $user = $this->userService->profilePic($request->image);

        return $this->apiResponse(new UserResource($user));

    }

    public function leaderboard(LeaderBoardRequest $request)
    {
        $users = $this->userService->leaderboard($request->group_id);

        return $this->respondResource(UserResource::collection($users));
    }

    public function manageGroups(Request $request)
    {
        try {
            $userData = $request['user'];
            $user = User::where('ar_token', $userData['arToken'])->first();
            $groupIds = $request['groupIds'];
            $this->userService->updateGroups($groupIds, $user->id);

            return true;
        } catch (Exception $e) {
            return $e;
        }
    }
}
