<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Repositories\Contracts\ReportShareRepositoryContract;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportShareController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected ReportShareRepositoryContract $reportShareRepository,
    ) {}

    public function store(Request $request, Report $report)
    {
        $this->authorize('share', $report);

        $validated = $request->validate([
            'user_ids' => ['array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $this->reportService->shareWith($report, $validated['user_ids'] ?? []);

        return back()->with('success', 'Sharing updated.');
    }

    public function destroy(Report $report)
    {
        $this->authorize('share', $report);

        $this->reportShareRepository->removeAll($report);

        return back()->with('success', 'Sharing removed.');
    }
}
