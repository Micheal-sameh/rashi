<?php

namespace App\Repositories;

use App\Models\QuestionAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionAnswerRepository extends BaseRepository
{
    public function __construct(QuestionAnswer $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return QuestionAnswer::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function index()
    {
        $query = $this->model->latest('date');

        return $this->execute($query);
    }

    public function show($id)
    {
        return $this->findById($id);
    }

    public function store($question_id, $input, $is_correct)
    {
        return $this->model->create([
            'answer' => $input,
            'is_correct' => $is_correct,
            'quiz_question_id' => $question_id,
        ]);
    }

    public function update($id, $input, $is_correct)
    {
        $competition = $this->findById($id);
        $competition->update([
            'answer' => $input,
            'is_correct' => $is_correct,
        ]);

        return $competition;
    }
}
