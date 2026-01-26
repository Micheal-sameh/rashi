<?php

namespace App\Jobs;

use App\Exports\CompetitionRankingPDF;
use App\Exports\CompetitionResultsExport;
use App\Mail\CompetitionFinishedMail;
use App\Models\Competition;
use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendCompetitionFinishedReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $competitionId;

    /**
     * Create a new job instance.
     */
    public function __construct($competitionId)
    {
        $this->competitionId = $competitionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $competition = Competition::with(['groups.users', 'quizzes'])->findOrFail($this->competitionId);

        // Process each group separately
        foreach ($competition->groups as $group) {
            // Find admin users in this group
            $adminUsers = $group->users()->role('admin')->get();

            if ($adminUsers->isEmpty()) {
                Log::warning("Group {$group->name} has no admin users. Skipping email.");

                continue;
            }

            try {
                // Initialize Google Drive service
                $googleDrive = new GoogleDriveService;

                // Generate Excel file
                $excelExport = new CompetitionResultsExport($competition, $group);
                $excelFileName = 'competition-results-'.$competition->id.'-group-'.$group->id.'.xlsx';
                $excelPath = storage_path('app/temp/'.$excelFileName);

                // Ensure temp directory exists
                if (! file_exists(storage_path('app/temp'))) {
                    mkdir(storage_path('app/temp'), 0755, true);
                }

                Excel::store($excelExport, 'temp/'.$excelFileName, 'local');

                // Generate PDF content in memory
                $pdfExport = new CompetitionRankingPDF($competition, $group);
                $mpdf = $pdfExport->generate();
                $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
                $pdfFileName = 'competition-rankings-'.$competition->name.'-'.$group->name.'.pdf';

                // Upload PDF to Google Drive
                $pdfUploadResult = $googleDrive->uploadFile($pdfContent, $pdfFileName, 'application/pdf');
                $pdfDriveLink = $pdfUploadResult['link'];

                // Send email to all admin users in this group
                foreach ($adminUsers as $admin) {
                    Mail::to($admin->email)->send(
                        new CompetitionFinishedMail($competition, $group, $excelPath, $pdfDriveLink)
                    );
                    Log::info("Competition finished email sent to {$admin->email} for group {$group->name}");
                }

                // Clean up temporary files
                @unlink($excelPath);

            } catch (\Exception $e) {
                Log::error("Failed to send competition finished email for group {$group->name}: ".$e->getMessage());
            }
        }
    }
}
