<?php

namespace App\Jobs;

use App\Exports\CompetitionLeaderboardExport;
use App\Mail\CompetitionLeaderboardExportMail;
use App\Models\Setting;
use App\Models\User;
use App\Services\CompetitionService;
use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class ProcessCompetitionLeaderboardExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $competitionId,
        protected array $userIds = [],
        protected $groupId = null
    ) {}

    public function handle(CompetitionService $competitionService): void
    {
        try {
            $leaderboardData = $competitionService->getLeaderboardExportData(
                $this->competitionId,
                $this->userIds,
                $this->groupId
            );

            $competition = $leaderboardData['competition'];
            $userStats = $leaderboardData['userStats'];
            $groupRankings = $leaderboardData['groupRankings'];
            $selectedGroupName = $leaderboardData['selectedGroupName'];
            $baseFileName = $leaderboardData['baseFileName'];
            $groupId = $leaderboardData['groupId'];

            $pdfFileName = $baseFileName.' - leaderboard.pdf';
            $excelFileName = $baseFileName.' - leaderboard.xlsx';

            $logo = Setting::where('name', 'logo')->first();

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'arial',
            ]);

            $html = view('competitions.leaderboard_pdf', compact('competition', 'userStats', 'groupRankings', 'logo'))->render();
            $mpdf->WriteHTML($html);

            $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
            $excelContent = Excel::raw(
                new CompetitionLeaderboardExport($competition, $userStats, $groupRankings),
                ExcelWriter::XLSX
            );

            $googleDrive = new GoogleDriveService;
            $pdfDriveLink = $googleDrive->uploadFile($pdfContent, $pdfFileName, 'application/pdf')['link'] ?? null;
            $excelDriveLink = $googleDrive->uploadFile(
                $excelContent,
                $excelFileName,
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            )['link'] ?? null;

            $adminQuery = User::query()->role('admin');
            if ($groupId) {
                $adminQuery->whereHas('groups', function ($query) use ($groupId) {
                    $query->where('groups.id', $groupId);
                });
            }

            $adminEmails = $adminQuery->pluck('email')->filter()->unique()->values();

            if ($adminEmails->isEmpty()) {
                $adminEmails = User::query()->role('admin')->pluck('email')->filter()->unique()->values();
            }

            foreach ($adminEmails as $email) {
                Mail::to($email)->send(new CompetitionLeaderboardExportMail(
                    $competition,
                    $selectedGroupName,
                    $pdfDriveLink,
                    $excelDriveLink,
                    $pdfContent,
                    $excelContent,
                    $pdfFileName,
                    $excelFileName
                ));
            }
        } catch (\Throwable $exception) {
            Log::error('Queued leaderboard export upload/email failed: '.$exception->getMessage(), [
                'competition_id' => $this->competitionId,
                'group_id' => $this->groupId,
            ]);
        }
    }
}
