<?php

namespace App\Console\Commands;

use App\Enums\ScheduleFrequency;
use App\Models\ReportSchedule;
use App\Repositories\Contracts\ReportScheduleRepositoryContract;
use App\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RunScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:run-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run any scheduled reports due to be emailed out right now';

    public function handle(ReportScheduleRepositoryContract $scheduleRepository, ReportService $reportService)
    {
        $now = Carbon::now();

        $due = $scheduleRepository->allDue($now)->filter(fn (ReportSchedule $schedule) => $this->isDue($schedule, $now));

        $this->info("Found {$due->count()} schedule(s) due.");

        foreach ($due as $schedule) {
            $reportService->runSchedule($schedule);
        }

        return 0;
    }

    /**
     * day_of_week uses Carbon's convention: 0 = Sunday .. 6 = Saturday.
     */
    protected function isDue(ReportSchedule $schedule, Carbon $now): bool
    {
        return match ($schedule->frequency) {
            ScheduleFrequency::DAILY => true,
            ScheduleFrequency::WEEKLY => (int) $now->dayOfWeek === (int) $schedule->day_of_week,
            ScheduleFrequency::MONTHLY => (int) $now->day === (int) $schedule->day_of_month,
        };
    }
}
