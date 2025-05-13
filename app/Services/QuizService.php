<?php

namespace App\Services;

use App\Repositories\QuizRepository;

class QuizService
{
    public function __construct(protected QuizRepository $quizRepository) {}

    public function index()
    {
        $competitions = $this->quizRepository->index();

        return $competitions;
    }

    public function show($id)
    {
        $competition = $this->quizRepository->show($id);

        return $competition;
    }

    public function store($input)
    {
        return $this->quizRepository->store($input);
    }

    public function update($id, $input)
    {
        return $this->quizRepository->update($id, $input);
    }
}
