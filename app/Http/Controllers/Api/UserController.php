<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateProfilePicRequest;
use App\Http\Requests\LeaderBoardRequest;
use App\Http\Resources\LeaderBoardResource;
use App\Http\Resources\UserResource;
use App\Services\UserService;

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

        return $this->respondResource(LeaderBoardResource::collection($users));
    }
}
