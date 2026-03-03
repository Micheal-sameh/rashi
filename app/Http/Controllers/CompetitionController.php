<?php

namespace App\Http\Controllers;

use App\DTOs\CompetitionCreateDTO;
use App\Exports\CompetitionExport;
use App\Http\Requests\CreateCompetitionRequest;
use App\Imports\QuizImport;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Services\CompetitionService;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class CompetitionController extends Controller
{
    public function __construct(
        protected CompetitionService $competitionService,
        protected GroupRepository $groupRepository,
    ) {
        //
    }

    public function index()
    {
        $competitions = $this->competitionService->index();
        $competitionCounts = $this->competitionService->getCompetitionCounts();

        return view('competitions.index', compact('competitions', 'competitionCounts'));
    }

    public function create()
    {
        $groups = $this->groupRepository->dropdown();

        return view('competitions.create', compact('groups'));
    }

    public function store(CreateCompetitionRequest $request)
    {
        $input = new CompetitionCreateDTO(...$request->only(
            'name', 'start_at', 'end_at', 'groups'
        ));

        $this->competitionService->store($input, $request->image);

        return redirect()->route('competitions.index')->with('success', 'Competition created successfully');

    }

    public function edit($id)
    {
        $competition = $this->competitionService->show($id)->load('groups', 'media');
        $groups = $this->groupRepository->dropdown();

        return view('competitions.edit', compact('competition', 'groups'));
    }

    public function update($id, CreateCompetitionRequest $request)
    {
        $input = new CompetitionCreateDTO(...$request->only(
            'name', 'start_at', 'end_at', 'groups'
        ));

        $this->competitionService->update($id, $input, $request->image);

        return redirect()->route('competitions.index')->with('success', 'Competition updated successfully');
    }

    public function cancel($id)
    {
        $this->competitionService->cancel($id);

        return redirect()->route('competitions.index')->with('success', 'Competition cancelled successfully');
    }

    public function changeStatus($id)
    {
        $data = $this->competitionService->changeStatus($id);

        return response()->json([
            'competition' => $data['status'],
            'status_class' => $data['statusClass'],
        ]);
    }

    public function setActive($id)
    {
        $this->competitionService->setStatus($id, \App\Enums\CompetitionStatus::ACTIVE);

        return redirect()->route('competitions.index')->with('success', 'Competition set to active successfully');
    }

    public function userAnswers($id)
    {
        $userIds = request('user_ids', []);
        $groupId = request('group_id');

        $competition = $this->competitionService->show($id)->load([
            'quizzes.questions.answers',
            'quizzes.questions.userAnswers' => function ($query) use ($userIds) {
                if (! empty($userIds)) {
                    $query->whereIn('user_id', $userIds);
                }
                $query->with(['user', 'answer']);
            },
        ]);

        // Get users based on group filter or all users
        $users = $this->competitionService->getUsersForCompetition($competition, $groupId);

        // Calculate user stats for each quiz, filtered by selected users
        $quizStats = [];
        foreach ($competition->quizzes as $quiz) {
            $quizStats[$quiz->id] = $this->competitionService->getUserStatsForQuiz($quiz, $userIds);
        }

        return view('competitions.user-answers', compact('competition', 'users', 'quizStats'));
    }

    public function exportLeaderboard($id)
    {
        $userIds = request('user_ids', []);
        $groupId = request('group_id');

        if (empty($userIds) && $groupId) {
            $competitionForUsers = $this->competitionService->show($id);
            $userIds = $this->competitionService->getUsersForCompetition($competitionForUsers, $groupId)
                ->pluck('id')
                ->toArray();
        }

        $competition = $this->competitionService->show($id)->load([
            'groups',
            'quizzes.questions.answers',
            'quizzes.questions.userAnswers' => function ($query) use ($userIds) {
                if (! empty($userIds)) {
                    $query->whereIn('user_id', $userIds);
                }
                $query->with(['user', 'answer']);
            },
        ]);

        $userStats = [];
        foreach ($competition->quizzes as $quiz) {
            $quizStats = $this->competitionService->getUserStatsForQuiz($quiz, $userIds);
            foreach ($quizStats as $userId => $stats) {
                if (! isset($userStats[$userId])) {
                    $userStats[$userId] = [
                        'name' => $stats['name'],
                        'total_correct' => 0,
                        'total_points' => 0,
                        'total_questions' => 0,
                    ];
                }
                $userStats[$userId]['total_correct'] += $stats['total_correct'];
                $userStats[$userId]['total_points'] += $stats['total_points'];
                $userStats[$userId]['total_questions'] += $stats['total_questions'];
            }
        }

        // Sort by total_points descending
        uasort($userStats, function ($a, $b) {
            return $b['total_points'] <=> $a['total_points'];
        });

        $competitionGroupIds = $competition->groups->pluck('id');
        $usersWithGroups = User::query()
            ->whereIn('id', array_keys($userStats))
            ->with(['groups' => function ($query) use ($competitionGroupIds) {
                $query->whereIn('groups.id', $competitionGroupIds);
            }])
            ->get()
            ->keyBy('id');

        foreach ($userStats as $userId => &$stats) {
            $primaryGroup = optional($usersWithGroups->get($userId))->groups->first();
            $stats['group_id'] = $primaryGroup?->id;
            $stats['group_name'] = $primaryGroup?->name ?? 'غير محدد';
        }
        unset($stats);

        $groupRankings = [];
        foreach ($userStats as $userId => $stats) {
            $groupKey = $stats['group_id'] ?? 'ungrouped';
            if (! isset($groupRankings[$groupKey])) {
                $groupRankings[$groupKey] = [
                    'title' => $stats['group_name'],
                    'users' => [],
                ];
            }

            $groupRankings[$groupKey]['users'][] = array_merge($stats, ['user_id' => $userId]);
        }

        foreach ($groupRankings as &$groupData) {
            usort($groupData['users'], function ($a, $b) {
                return $b['total_points'] <=> $a['total_points'];
            });
        }
        unset($groupData);

        $logo = \App\Models\Setting::where('name', 'logo')->first();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'arial',
        ]);
        // 'export'
        $html = view('competitions.leaderboard_pdf', compact('competition', 'userStats', 'groupRankings', 'logo'))->render();

        $mpdf->WriteHTML($html);

        return $mpdf->Output("{$competition->name}.pdf", 'D');
    }

    public function uploadQuizzes($id)
    {
        request()->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new QuizImport($id), request()->file('file'));

            if (request()->expectsJson()) {
                return response()->json(['message' => 'Quizzes uploaded successfully'], 200);
            }

            return redirect()->route('competitions.index')
                ->with('success', 'Quizzes uploaded successfully');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: ".implode(', ', $failure->errors());
            }

            $errorMessage = 'Validation errors in Excel file: '.implode(' | ', $errors);

            if (request()->expectsJson()) {
                return response()->json(['message' => $errorMessage], 422);
            }

            return redirect()->route('competitions.index')
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error uploading quizzes: '.$e->getMessage();

            if (request()->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }

            return redirect()->route('competitions.index')
                ->with('error', $errorMessage);
        }
    }

    public function downloadExampleExcel()
    {
        return Excel::download(new \App\Exports\QuizExampleExport, 'quiz_example.xlsx');
    }

    public function exportCompetition($id)
    {
        $competition = $this->competitionService->show($id);

        return Excel::download(
            new CompetitionExport($competition),
            'competition_'.$competition->name.'_'.date('Y-m-d_H-i-s').'.xlsx'
        );
    }
}
