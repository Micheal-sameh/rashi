<?php

namespace App\Repositories;

use App\Models\PointHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PointHistoryRepository extends BaseRepository
{
    public function __construct(PointHistory $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return PointHistory::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function store($data)
    {
        $user = Auth::user();

        return $this->model->create([
            'user_id' => $user->id,
            'amount' => $data['score'],
            'points' => $user->points + $data['score'],
            'score' => $user->score + $data['score'],
            'subject_id' => $data['subject']->id,
            'subject_type' => get_class($data['subject']),
        ]);
    }
}
