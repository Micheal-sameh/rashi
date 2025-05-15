<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserAnswerCreateRequest;
use App\Http\Resources\UserAnswerResource;
use App\Repositories\PointHistoryRepository;
use App\Services\UserAnswerService;

class UserAnswerController extends BaseController
{
    public function __construct(
        protected UserAnswerService $userAnswerService,
        protected PointHistoryRepository $pointHistoryRepository,
    ) {}

    public function store(UserAnswerCreateRequest $request)
    {
        $data = $this->userAnswerService->store($request->questions);
        $this->pointHistoryRepository->store($data);

        return $this->apiResponse(new UserAnswerResource($data));
    }
}
