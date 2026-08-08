<?php

namespace App\Repositories;

use App\Models\Report;
use App\Repositories\Contracts\ReportShareRepositoryContract;

class ReportShareRepository extends BaseRepository implements ReportShareRepositoryContract
{
    protected function model(): string
    {
        return Report::class;
    }

    public function __construct(Report $model)
    {
        parent::__construct($model);
    }

    public function sync(Report $report, array $userIds): void
    {
        $report->sharedWith()->sync($userIds);
    }

    public function removeAll(Report $report): void
    {
        $report->sharedWith()->sync([]);
    }
}
