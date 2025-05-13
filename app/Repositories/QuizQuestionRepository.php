<?php

namespace App\Repositories;

use App\Models\QuizQuestion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class QuizQuestionRepository extends BaseRepository
{
    public function __construct(QuizQuestion $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return QuizQuestion::class;
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

    public function store($quiz, $input)
    {
        $question = $this->model->create([
            'question' => $input['question'],
            'points' => $input['points'],
            'quiz_id' => $quiz->id,
        ]);

        return $question->id;
    }

    public function update($id, $input)
    {
        $competition = $this->findById($id);
        $competition->update([
            'name' => $input->name,
            'date' => Carbon::parse($input->date),
            'competition_id' => $input->competition_id,
        ]);

        return $competition;
    }
}
