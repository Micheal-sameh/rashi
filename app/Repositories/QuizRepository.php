<?php

namespace App\Repositories;

use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class QuizRepository extends BaseRepository
{
    public function __construct(Quiz $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return Quiz::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function index($competition_id)
    {
        $query = $this->model
            ->when(isset($competition_id), fn ($q) => $q->where('competition_id', $competition_id))
            ->latest('date');

        return $this->execute($query);
    }

    public function show($id)
    {
        return $this->findById($id);
    }

    public function store($input)
    {
        return $this->model->create([
            'name' => $input->name,
            'date' => Carbon::parse($input->date),
            'competition_id' => $input->competition_id,
        ]);
    }

    public function update($id, $input)
    {
        $quiz = $this->findById($id);
        $quiz->update([
            'name' => $input->name,
            'date' => Carbon::parse($input->date),
        ]);

        return $quiz;
    }

    public function dropdown($id)
    {
        return $this->model->where('competition_id', $id)->get();
    }

    public function delete($id)
    {
        $quiz = $this->findById($id);
        $quiz->questions?->each(function ($question) {
            $question->answers->each(function ($answer) {
                $answer->delete();
            });
            $question->delete();
        });
        $quiz->delete();
    }
}
