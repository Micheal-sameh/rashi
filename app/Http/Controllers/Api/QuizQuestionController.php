<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\QuestionIndexRequest;
use App\Http\Resources\QuizQuestionResource;
use App\Services\QuizQuestionService;
use Carbon\Carbon;

class QuizQuestionController extends BaseController
{
    public function __construct(protected QuizQuestionService $quizQuestionService) {}

    public function index(QuestionIndexRequest $request)
    {
        $questions = $this->quizQuestionService->index($request);

        return $this->respondResource(QuizQuestionResource::collection($questions),
            additional_data: [
                'quiz_have_solved' => ! $questions->first()->quiz->isSolved->isEmpty(),
                'show_results' => (Carbon::parse($questions->first()->quiz->date)->lt(today())),
            ]
        );
    }
}
