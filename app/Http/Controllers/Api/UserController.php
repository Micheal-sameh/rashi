<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateProfilePicRequest;
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

    public function profilePic(CreateProfilePicRequest $request)
    {
        $user = $this->userService->profilePic($request->image);

        return $this->apiResponse(new UserResource($user));

    }
}
