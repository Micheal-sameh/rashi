<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserAnswerCreateRequest;
use App\Models\PointHistory;
use App\Repositories\PointHistoryRepository;
use App\Repositories\UserRepository;
use App\Services\UserAnswerService;

class UserAnswerController extends BaseController
{
    public function __construct(
        protected UserAnswerService $userAnswerService,
        protected PointHistoryRepository $pointHistoryRepository,
        protected UserRepository $userRepository,
    ) {}

    public function store(UserAnswerCreateRequest $request)
    {
        $data = $this->userAnswerService->store($request->validated());

        // if ($data['correct_answers'] > 0) {
        PointHistory::addRecord(collect($data));
        $this->userRepository->updatePoints($data);
        // }

        return $this->apiResponse();
    }
}
