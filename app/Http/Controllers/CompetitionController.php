<?php

namespace App\Http\Controllers;

use App\DTOs\CompetitionCreateDTO;
use App\Http\Requests\CreateCompetitionRequest;
use App\Repositories\GroupRepository;
use App\Services\CompetitionService;
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

        return view('competitions.index', compact('competitions'));
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
        $userId = request('user_id');
        $competition = $this->competitionService->show($id)->load([
            'quizzes.questions.answers',
            'quizzes.questions.userAnswers' => function ($query) use ($userId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                }
                $query->with(['user', 'answer']);
            },
        ]);

        $users = $this->competitionService->getUsersForCompetition($competition);

        // Calculate user stats for each quiz
        $quizStats = [];
        foreach ($competition->quizzes as $quiz) {
            $quizStats[$quiz->id] = $this->competitionService->getUserStatsForQuiz($quiz);
        }

        return view('competitions.user-answers', compact('competition', 'users', 'userId', 'quizStats'));
    }

    public function exportLeaderboard($id)
    {
        $competition = $this->competitionService->show($id)->load([
            'quizzes.questions.answers',
            'quizzes.questions.userAnswers' => function ($query) {
                $query->with(['user', 'answer']);
            },
        ]);

        $userStats = [];
        foreach ($competition->quizzes as $quiz) {
            $quizStats = $this->competitionService->getUserStatsForQuiz($quiz);
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

        $logo = \App\Models\Setting::where('name', 'logo')->first();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'arial',
        ]);
        // 'export'
        $html = view('competitions.leaderboard_pdf', compact('competition', 'userStats', 'logo'))->render();

        $mpdf->WriteHTML($html);

        return $mpdf->Output("{$competition->name}.pdf", 'D');
    }
}
