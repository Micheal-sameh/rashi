<?php

namespace App\Repositories;

use App\Models\Report;
use App\Repositories\Contracts\ReportRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class ReportRepository extends BaseRepository implements ReportRepositoryContract
{
    protected function model(): string
    {
        return Report::class;
    }

    public function __construct(Report $model)
    {
        parent::__construct($model);
    }

    public function all(int $userId): Collection
    {
        return $this->model->visibleTo($userId)->latest()->get();
    }

    public function myReports(int $userId): Collection
    {
        return $this->model->where('created_by', $userId)->latest()->get();
    }

    public function sharedWithMe(int $userId): Collection
    {
        return $this->model->whereHas('sharedWith', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->latest()->get();
    }

    public function find(int $id): Report
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Report
    {
        return $this->model->create($data);
    }

    public function update(Report $report, array $data): bool
    {
        return $report->update($data);
    }

    public function delete(Report $report): bool
    {
        return $report->delete();
    }
}
