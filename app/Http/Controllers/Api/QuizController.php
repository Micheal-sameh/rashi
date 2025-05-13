<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\QuizResource;
use App\Services\QuizService;

class QuizController extends BaseController
{
    public function __construct(protected QuizService $quizService) {}

    public function index()
    {
        $competitions = $this->quizService->index();

        return $this->respondResource(QuizResource::collection($competitions));
    }
}
