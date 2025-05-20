<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\QuestionIndexRequest;
use App\Http\Resources\QuizQuestionResource;
use App\Services\QuizQuestionService;

class QuizQuestionController extends BaseController
{
    public function __construct(protected QuizQuestionService $quizQuestionService) {}

    public function index(QuestionIndexRequest $request)
    {
        $questions = $this->quizQuestionService->index($request);

        return $this->respondResource(QuizQuestionResource::collection($questions));
    }
}
