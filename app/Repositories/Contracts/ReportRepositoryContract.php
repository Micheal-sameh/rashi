<?php

namespace App\Repositories\Contracts;

use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;

interface ReportRepositoryContract
{
    public function all(int $userId): Collection;

    public function myReports(int $userId): Collection;

    public function sharedWithMe(int $userId): Collection;

    public function find(int $id): Report;

    public function create(array $data): Report;

    public function update(Report $report, array $data): bool;

    public function delete(Report $report): bool;
}
