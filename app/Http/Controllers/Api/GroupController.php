<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GroupResource;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends BaseController
{
    public function __construct(
        protected GroupService $groupService,
        protected GroupRepository $groupRepository
    ) {}

    public function create(Request $request)
    {
        $groups = $this->groupService->create($request->group);

        return $this->respondResource(GroupResource::collection($groups));
    }

    public function update($id, Request $request)
    {
        $groups = $this->groupService->update($id, $request->group);

        return $this->respondResource(GroupResource::collection($groups));
    }

    public function manageUsers(Request $request)
    {
        $group = $request->group;
        $arTokens = collect($request->users)->flatten()->toArray();

        $userIds = User::whereIn('ar_token', $arTokens)->pluck('id');
        $groups = $this->groupRepository->updateUsers($group['id'], $userIds);

        return $this->respondResource(GroupResource::collection($groups));
    }
}
