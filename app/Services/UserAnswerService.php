<?php

namespace App\Services;

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
        $correctAnswers = $score = 0;
        for ($i = 0; $i < count($input); $i++) {
            $isCorrect = $this->questionAnswerRepository->isCorrect($input[$i]);
            $points = 0;
            if ($isCorrect) {
                $quizQuestion = $this->quizQuestionRepository->findById($input[$i]['question_id']);
                $points = $quizQuestion->points;
            }
            $correctAnswers = $isCorrect ? $correctAnswers + 1 : $correctAnswers;
            $score += $points;
            $this->userAnswerRepository->store($input[$i], $points);
        }
        $data['correct_answers'] = $correctAnswers;
        $data['score'] = $score;
        $data['total_questions'] = count($input);
        $data['subject'] = $quizQuestion->quiz;

        return $data;
    }
}
