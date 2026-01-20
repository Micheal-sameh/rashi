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
        $questions = $this->quizQuestionRepository->index($input);

        return $questions;
    }

    public function show($id)
    {
        $question = $this->quizQuestionRepository->show($id);
        $question->load('answers', 'quiz');

        return $question;
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
        $question = $this->quizQuestionRepository->update($id, $input);

        // Delete existing answers
        $question->answers()->delete();

        // Create new answers
        foreach ($input->answers as $index => $answer) {
            $this->questionAnswerRepository->store($question->id, $answer, $input->correct == $index ? 1 : 0);
        }

        return $question;
    }

    public function create($input)
    {
        $question = $this->quizQuestionRepository->create($input);
        foreach ($input->answers as $index => $answer) {
            $this->questionAnswerRepository->store($question->id, $answer, $input->correct == $index ? 1 : 0);
        }

        return $question;
    }

    public function delete($id)
    {
        return $this->quizQuestionRepository->delete($id);
    }
}
