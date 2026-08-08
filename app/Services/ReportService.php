<?php

namespace App\Services;

use App\DTOs\BuildReportDTO;
use App\DTOs\SaveReportDTO;
use App\DTOs\ScheduleReportDTO;
use App\Enums\ScheduleStatus;
use App\Mail\ScheduledReportMail;
use App\Models\Report;
use App\Models\ReportSchedule;
use App\Repositories\Contracts\ReportRepositoryContract;
use App\Repositories\Contracts\ReportScheduleRepositoryContract;
use App\Repositories\Contracts\ReportShareRepositoryContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReportService
{
    public function __construct(
        protected ReportRepositoryContract $reportRepository,
        protected ReportShareRepositoryContract $reportShareRepository,
        protected ReportScheduleRepositoryContract $reportScheduleRepository,
        protected ReportBuilderService $reportBuilderService,
    ) {}

    public function save(SaveReportDTO $dto): Report
    {
        $data = [
            'name' => $dto->name,
            'description' => $dto->description,
            'table_name' => $dto->tableName,
            'columns' => $dto->columns,
            'filters' => array_map(fn ($filter) => $filter->toArray(), $dto->filters),
            'sort_column' => $dto->sortColumn,
            'sort_direction' => $dto->sortDirection,
            'created_by' => $dto->createdBy,
        ];

        return $this->reportRepository->create($data);
    }

    public function update(Report $report, SaveReportDTO $dto): bool
    {
        return $this->reportRepository->update($report, [
            'name' => $dto->name,
            'description' => $dto->description,
            'table_name' => $dto->tableName,
            'columns' => $dto->columns,
            'filters' => array_map(fn ($filter) => $filter->toArray(), $dto->filters),
            'sort_column' => $dto->sortColumn,
            'sort_direction' => $dto->sortDirection,
            'updated_by' => Auth::id(),
        ]);
    }

    public function shareWith(Report $report, array $userIds): void
    {
        if ($report->created_by !== Auth::id()) {
            abort(403, 'Only the report owner can manage sharing.');
        }

        $this->reportShareRepository->sync($report, $userIds);
    }

    public function schedule(ScheduleReportDTO $dto): ReportSchedule
    {
        return $this->reportScheduleRepository->create([
            'report_id' => $dto->reportId,
            'frequency' => $dto->frequency,
            'time' => $dto->time,
            'day_of_week' => $dto->dayOfWeek,
            'day_of_month' => $dto->dayOfMonth,
            'recipients' => $dto->recipients,
            'is_active' => $dto->isActive,
        ]);
    }

    public function runSchedule(ReportSchedule $schedule): void
    {
        $report = $schedule->report;

        try {
            $buildDto = new BuildReportDTO(
                tableName: $report->table_name,
                columns: $report->columns,
                filters: array_map(
                    fn (array $filter) => \App\DTOs\ReportFilterDTO::fromArray($filter),
                    $report->filters ?? []
                ),
                sortColumn: $report->sort_column,
                sortDirection: $report->sort_direction,
                perPage: config('report_builder.max_per_page', 100),
                page: 1,
            );

            $rows = $this->reportBuilderService->run($buildDto)->items();

            foreach ($schedule->recipients as $recipient) {
                Mail::to($recipient)->send(new ScheduledReportMail($report, $rows));
            }

            $this->reportScheduleRepository->update($schedule, [
                'last_run_at' => now(),
            ]);

            $schedule->logs()->create([
                'status' => ScheduleStatus::SUCCESS,
                'row_count' => count($rows),
                'ran_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $schedule->logs()->create([
                'status' => ScheduleStatus::FAILED,
                'error' => $e->getMessage(),
                'ran_at' => now(),
            ]);
        }
    }
}
