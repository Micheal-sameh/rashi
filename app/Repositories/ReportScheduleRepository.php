<?php

namespace App\Repositories;

use App\Models\ReportSchedule;
use App\Repositories\Contracts\ReportScheduleRepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ReportScheduleRepository extends BaseRepository implements ReportScheduleRepositoryContract
{
    protected function model(): string
    {
        return ReportSchedule::class;
    }

    public function __construct(ReportSchedule $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): ReportSchedule
    {
        return $this->model->create($data);
    }

    public function update(ReportSchedule $schedule, array $data): bool
    {
        return $schedule->update($data);
    }

    public function delete(ReportSchedule $schedule): bool
    {
        return $schedule->delete();
    }

    public function allDue(Carbon $now): Collection
    {
        $windowStart = $now->copy()->subMinutes(5)->format('H:i:s');
        $windowEnd = $now->copy()->addMinutes(5)->format('H:i:s');

        return $this->model
            ->with('report')
            ->where('is_active', true)
            ->whereTime('time', '>=', $windowStart)
            ->whereTime('time', '<=', $windowEnd)
            ->get();
    }
}
