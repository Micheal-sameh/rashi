<?php

namespace App\Repositories;

use App\Models\PointHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function userHistory($id)
    {
        $query = $this->model->where('user_id', $id)->latest();

        return $this->execute($query);
    }
}
