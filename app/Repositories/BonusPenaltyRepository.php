<?php

namespace App\Repositories;

use App\Models\BonusPenalty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BonusPenaltyRepository extends BaseRepository
{
    public function __construct(BonusPenalty $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return BonusPenalty::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function index($user_id = null)
    {
        $query = $this->model->with(['user', 'creator'])
            ->when(isset($user_id), fn ($q) => $q->where('user_id', $user_id))
            ->latest();

        return $this->execute($query);
    }

    public function store($data)
    {
        return $this->model->create($data);
    }
}
