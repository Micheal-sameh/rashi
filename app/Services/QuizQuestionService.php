<?php

namespace App\Services;

use App\Repositories\QuestionAnswerRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\QuizRepository;
use Illuminate\Support\Facades\DB;

class QuizQuestionService
{
    public function __construct(
        protected QuizRepository $quizRepository,
        protected QuizQuestionRepository $quizQuestionRepository,
        protected QuestionAnswerRepository $questionAnswerRepository,
    ) {}

    public function index($input)
    {
        $quizzes = $this->quizQuestionRepository->index($input);

        return $quizzes;
    }

    public function show($id)
    {
        $quiz = $this->quizQuestionRepository->show($id);

        return $quiz;
    }

    public function store($input)
    {
        DB::beginTransaction();
        $quiz = $this->quizQuestionRepository->store($input);
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
        return $this->quizQuestionRepository->update($id, $input);
    }

    public function create($input)
    {
        $question = $this->quizQuestionRepository->create($input);
        for ($i = 0; $i < 4; $i++) {
            $this->questionAnswerRepository->store($question->id, $input->answers[$i + 1], $input->correct == $i ? 1 : 0);
        }

        return $question;

    }
}
