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

    public function index($competition_id, $search = null)
    {
        $query = $this->model->query()
            ->with('competition:id,name')
            ->when(isset($competition_id), fn ($q) => $q->where('competition_id', $competition_id))
            ->when(isset($search), fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->orderByRaw('DATE(date) = CURDATE() DESC') // put today's first
            ->orderByDesc('date');

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
            'help' => $input->help,
        ]);
    }

    public function update($id, $input)
    {
        $quiz = $this->findById($id);

        $updateData = ['name' => $input['name']];

        // Only update date if it is provided
        if (isset($input['date'])) {
            $updateData['date'] = Carbon::parse($input['date']);
        }

        // Update help if provided
        if (isset($input['help'])) {
            $updateData['help'] = $input['help'];
        }

        $quiz->update($updateData);

        return $quiz;
    }

    public function dropdown($id)
    {
        return $this->model->where('competition_id', $id)->get();
    }

    public function delete($id)
    {
        $quiz = $this->findById($id);

        // Use eager loading with bulk deletes to prevent N+1
        $quiz->load('questions.answers');

        // Get all IDs at once
        $questionIds = $quiz->questions->pluck('id');

        if ($questionIds->isNotEmpty()) {
            // Bulk delete answers
            \App\Models\QuestionAnswer::whereIn('quiz_question_id', $questionIds)->delete();
            // Bulk delete questions
            \App\Models\QuizQuestion::whereIn('id', $questionIds)->delete();
        }

        $quiz->delete();
    }
}
