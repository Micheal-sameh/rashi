<?php

namespace App\Http\Controllers;

use App\DTOs\ScheduleReportDTO;
use App\Models\Report;
use App\Models\ReportSchedule;
use App\Repositories\Contracts\ReportScheduleRepositoryContract;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportScheduleController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected ReportScheduleRepositoryContract $reportScheduleRepository,
    ) {}

    public function store(Request $request, Report $report)
    {
        $this->authorize('schedule', $report);

        $validated = $this->validateSchedulePayload($request);

        $dto = ScheduleReportDTO::fromArray([
            ...$validated,
            'report_id' => $report->id,
        ]);

        $this->reportService->schedule($dto);

        return back()->with('success', 'Schedule created.');
    }

    public function update(Request $request, ReportSchedule $schedule)
    {
        $this->authorize('schedule', $schedule->report);

        $validated = $this->validateSchedulePayload($request);

        $this->reportScheduleRepository->update($schedule, [
            'frequency' => $validated['frequency'],
            'time' => $validated['time'],
            'day_of_week' => $validated['day_of_week'] ?? null,
            'day_of_month' => $validated['day_of_month'] ?? null,
            'recipients' => $validated['recipients'] ?? [],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', 'Schedule updated.');
    }

    public function destroy(ReportSchedule $schedule)
    {
        $this->authorize('schedule', $schedule->report);

        $this->reportScheduleRepository->delete($schedule);

        return back()->with('success', 'Schedule removed.');
    }

    protected function validateSchedulePayload(Request $request): array
    {
        return $request->validate([
            'frequency' => ['required', 'in:daily,weekly,monthly'],
            'time' => ['required', 'date_format:H:i'],
            'day_of_week' => ['nullable', 'integer', 'min:0', 'max:6'],
            'day_of_month' => ['nullable', 'integer', 'min:1', 'max:31'],
            'recipients' => ['array'],
            'recipients.*' => ['email'],
            'is_active' => ['boolean'],
        ]);
    }
}
