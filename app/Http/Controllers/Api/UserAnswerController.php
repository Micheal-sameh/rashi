<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserAnswerCreateRequest;
use App\Http\Resources\UserAnswerResource;
use App\Services\UserAnswerService;

class UserAnswerController extends BaseController
{
    public function __construct(protected UserAnswerService $userAnswerService) {}

    public function store(UserAnswerCreateRequest $request)
    {
        $data = $this->userAnswerService->store($request->questions);

        return $this->apiResponse(new UserAnswerResource($data));
    }
}
