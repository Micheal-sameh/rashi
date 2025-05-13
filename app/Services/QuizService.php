<?php

namespace App\Services;

use App\Repositories\QuestionAnswerRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\QuizRepository;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function __construct(
        protected QuizRepository $quizRepository,
        protected QuizQuestionRepository $quizQuestionRepository,
        protected QuestionAnswerRepository $questionAnswerRepository,
    ) {}

    public function index()
    {
        $quizzes = $this->quizRepository->index();

        return $quizzes;
    }

    public function show($id)
    {
        $quiz = $this->quizRepository->show($id);

        return $quiz;
    }

    public function store($input)
    {
        DB::beginTransaction();
        $quiz = $this->quizRepository->store($input);
        foreach ($input->questions as $question) {
            $question_id = $this->quizQuestionRepository->store($quiz, $question);
            $i = 0;
            foreach (collect($question['answers']) as $answer) {
                $this->questionAnswerRepository->store($question_id, $answer, $question['correct'] == $i ? 1 : 0);
                $i++;
            }
        }

        DB::commit();

        return $quiz;
    }

    public function update($id, $input)
    {
        return $this->quizRepository->update($id, $input);
    }
}
