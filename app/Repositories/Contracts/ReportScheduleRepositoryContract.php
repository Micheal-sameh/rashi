<?php

namespace App\Repositories\Contracts;

use App\Models\ReportSchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface ReportScheduleRepositoryContract
{
    public function create(array $data): ReportSchedule;

    public function update(ReportSchedule $schedule, array $data): bool;

    public function delete(ReportSchedule $schedule): bool;

    /**
     * Active schedules due to run within a 5 minute window of $now.
     */
    public function allDue(Carbon $now): Collection;
}
