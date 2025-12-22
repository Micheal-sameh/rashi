<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GroupResource;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends BaseController
{
    public function __construct(protected GroupService $groupService) {}

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
}
