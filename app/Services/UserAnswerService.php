<?php

namespace App\Services;

use App\Models\Quiz;
use App\Repositories\QuestionAnswerRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\UserAnswerRepository;

class UserAnswerService
{
    public function __construct(
        protected UserAnswerRepository $userAnswerRepository,
        protected QuestionAnswerRepository $questionAnswerRepository,
        protected QuizQuestionRepository $quizQuestionRepository,
    ) {}

    public function store($input)
    {
        $questions = $input['questions'];
        $quiz = Quiz::find($input['quiz_id']);
        $correctAnswers = $score = 0;
        for ($i = 0; $i < count($questions); $i++) {
            $isCorrect = $this->questionAnswerRepository->isCorrect($questions[$i]);
            $points = 0;
            if ($isCorrect) {
                $quizQuestion = $this->quizQuestionRepository->findById($questions[$i]['question_id']);
                $points = $quizQuestion->points;
            }
            $correctAnswers = $isCorrect ? $correctAnswers + 1 : $correctAnswers;
            $score += $points;
            $this->userAnswerRepository->store($questions[$i], $points);
        }
        $data['correct_answers'] = $correctAnswers;
        $data['score'] = $score;
        $data['total_questions'] = count($questions);
        $data['subject'] = $quiz ?? null;

        return $data;
    }
}
