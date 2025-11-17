<?php

namespace App\Repositories;

use App\Models\QuizQuestion;
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

    public function index($input)
    {
        $query = $this->model
            ->with(['userAnswer'])
            ->when(isset($input['quiz_id']), fn ($q) => $q->where('quiz_id', $input['quiz_id']))
            ->when(isset($input['competition_id']), function ($query) use ($input) {
                $query->whereHas('quiz', function ($query) use ($input) {
                    $query->whereHas('competition', fn ($q) => $q->where('id', $input['competition_id']));
                });
            });

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
        $question = $this->findById($id);
        $question->update([
            'question' => $input->question,
            'points' => $input->points,
        ]);

        return $question;
    }

    public function create($input)
    {
        $question = $this->model->create([
            'question' => $input->question,
            'points' => $input->points,
            'quiz_id' => $input->quiz_id,
        ]);

        return $question;
    }

    public function delete($id)
    {
        $question = $this->findById($id);
        $question->answers->each(function ($answer) {
            $answer->delete();
        });
        $question->delete();
    }
}
