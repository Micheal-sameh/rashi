<?php

namespace App\Repositories;

use App\Models\UserAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class UserAnswerRepository extends BaseRepository
{
    public function __construct(UserAnswer $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return UserAnswer::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function store($input, $points = 0)
    {
        return $this->model->create([
            'user_id' => Auth::id(),
            'quiz_question_id' => $input['question_id'],
            'question_answer_id' => $input['answer_id'],
            'points' => $points,
        ]);
    }

    protected function isCorrect($question_id, $answer_id) {}
}
