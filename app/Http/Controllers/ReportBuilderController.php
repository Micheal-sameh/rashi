<?php

namespace App\Http\Controllers;

use App\DTOs\BuildReportDTO;
use App\DTOs\SaveReportDTO;
use App\Models\Report;
use App\Models\User;
use App\Repositories\Contracts\ReportRepositoryContract;
use App\Services\ReportBuilderService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReportBuilderController extends Controller
{
    public function __construct(
        protected ReportRepositoryContract $reportRepository,
        protected ReportBuilderService $reportBuilderService,
        protected ReportService $reportService,
    ) {}

    public function index()
    {
        $userId = Auth::id();

        return Inertia::render('Reports/Index', [
            'reports' => $this->reportRepository->myReports($userId),
            'sharedReports' => $this->reportRepository->sharedWithMe($userId),
            'users' => User::select('id', 'name', 'email')->orderBy('name')->get(),
            'tables' => config('report_builder.tables'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Reports/Builder', [
            'tables' => config('report_builder.tables'),
            'operators' => config('report_builder.operators'),
            'users' => User::select('id', 'name', 'email')->orderBy('name')->get(),
        ]);
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);

        $report->load(['sharedWith:id,name,email', 'schedules']);

        return Inertia::render('Reports/Builder', [
            'report' => $report,
            'tables' => config('report_builder.tables'),
            'operators' => config('report_builder.operators'),
            'users' => User::select('id', 'name', 'email')->orderBy('name')->get(),
        ]);
    }

    public function run(Request $request)
    {
        $validated = $request->validate([
            'table_name' => ['required', 'string'],
            'columns' => ['array'],
            'columns.*' => ['string'],
            'filters' => ['array'],
            'filters.*.column' => ['required_with:filters', 'string'],
            'filters.*.operator' => ['required_with:filters', 'string'],
            'filters.*.value' => ['nullable'],
            'sort_column' => ['nullable', 'string'],
            'sort_direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $dto = BuildReportDTO::fromArray($validated);

        $results = $this->reportBuilderService->run($dto);

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $validated = $this->validateReportPayload($request);

        $dto = SaveReportDTO::fromArray([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        $report = $this->reportService->save($dto);

        return redirect()->route('reports.show', $report)->with('success', 'Report saved.');
    }

    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $validated = $this->validateReportPayload($request);

        $dto = SaveReportDTO::fromArray([
            ...$validated,
            'created_by' => $report->created_by,
        ]);

        $this->reportService->update($report, $dto);

        return redirect()->route('reports.show', $report)->with('success', 'Report updated.');
    }

    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);

        $this->reportRepository->delete($report);

        return redirect()->route('reports.index')->with('success', 'Report deleted.');
    }

    protected function validateReportPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'table_name' => ['required', 'string'],
            'columns' => ['required', 'array', 'min:1'],
            'columns.*' => ['string'],
            'filters' => ['array'],
            'filters.*.column' => ['required_with:filters', 'string'],
            'filters.*.operator' => ['required_with:filters', 'string'],
            'filters.*.value' => ['nullable'],
            'sort_column' => ['nullable', 'string'],
            'sort_direction' => ['nullable', 'in:asc,desc'],
        ]);
    }
}
