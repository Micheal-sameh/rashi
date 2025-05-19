<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\QuizIndexRequest;
use App\Http\Resources\QuizResource;
use App\Services\QuizService;

class QuizController extends BaseController
{
    public function __construct(protected QuizService $quizService) {}

    public function index(QuizIndexRequest $request)
    {
        $quizzes = $this->quizService->index($request->competition_id);

        return $this->respondResource(QuizResource::collection($quizzes));
    }

    public function dropdown($id)
    {
        $quizzes = $this->quizService->dropdown($id);

        return $this->apiResponse(QuizResource::collection($quizzes));
    }
}
