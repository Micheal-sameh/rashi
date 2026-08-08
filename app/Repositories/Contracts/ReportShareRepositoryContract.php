<?php

namespace App\Repositories\Contracts;

use App\Models\Report;

interface ReportShareRepositoryContract
{
    public function sync(Report $report, array $userIds): void;

    public function removeAll(Report $report): void;
}
