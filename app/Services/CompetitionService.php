<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\CompetitionRepository;
use Illuminate\Support\Facades\DB;

class CompetitionService
{
    public function __construct(protected CompetitionRepository $competitionRepository) {}

    public function index()
    {
        $competitions = $this->competitionRepository->index();

        return $competitions;
    }

    public function show($id)
    {
        $competition = $this->competitionRepository->show($id);

        return $competition;
    }

    public function store($input, $image)
    {
        return $this->competitionRepository->store($input, $image);
    }

    public function update($id, $input, $image)
    {
        return $this->competitionRepository->update($id, $input, $image);
    }

    public function cancel($id)
    {
        return $this->competitionRepository->cancel($id);
    }

    public function dropdown()
    {
        return $this->competitionRepository->dropdown();
    }

    public function changeStatus($id)
    {
        DB::beginTransaction();
        $data = $this->competitionRepository->changeStatus($id);
        DB::commit();

        return $data;
    }

    public function setStatus($id, $status)
    {
        DB::beginTransaction();
        $this->competitionRepository->setStatus($id, $status);
        DB::commit();
    }

    public function getUsersForCompetition($competition, $groupId = null)
    {
        return $this->competitionRepository->getUsersForCompetition($competition, $groupId);
    }

    public function getUsersForCompetitionById($competitionId, $groupId = null)
    {
        return $this->competitionRepository->getUsersForCompetitionById($competitionId, $groupId);
    }

    public function getCompetitionWithUserAnswers($competitionId, array $userIds = [])
    {
        return $this->competitionRepository->getCompetitionWithUserAnswers($competitionId, $userIds);
    }

    public function getUserStatsForQuiz($quiz, $userIds = [])
    {
        // Ensure relationships are loaded to prevent N+1
        if (! $quiz->relationLoaded('questions')) {
            $quiz->load(['questions.userAnswers.user', 'questions.userAnswers.answer']);
        }

        $userStats = [];
        foreach ($quiz->questions as $question) {
            foreach ($question->userAnswers as $userAnswer) {
                $userId = $userAnswer->user_id;

                // Skip if filtering by user IDs and this user is not in the list
                if (! empty($userIds) && ! in_array($userId, $userIds)) {
                    continue;
                }

                if (! isset($userStats[$userId])) {
                    $userStats[$userId] = [
                        'name' => $userAnswer->user->name,
                        'total_correct' => 0,
                        'total_points' => 0,
                        'total_questions' => 0,
                    ];
                }
                $userStats[$userId]['total_questions']++;
                if ($userAnswer->answer->is_correct) {
                    $userStats[$userId]['total_correct']++;
                }
                $userStats[$userId]['total_points'] += $userAnswer->points;
            }
        }

        return $userStats;
    }

    public function getCompetitionCounts()
    {
        return $this->competitionRepository->getCompetitionCounts();
    }

    public function getUserAnswersViewData($competitionId, array $userIds = [], $groupId = null): array
    {
        $competition = $this->getCompetitionWithUserAnswers($competitionId, $userIds);
        $users = $this->getUsersForCompetitionById($competitionId, $groupId);

        $quizStats = [];
        foreach ($competition->quizzes as $quiz) {
            $quizStats[$quiz->id] = $this->getUserStatsForQuiz($quiz, $userIds);
        }

        return compact('competition', 'users', 'quizStats');
    }

    public function getLeaderboardExportData($competitionId, array $userIds = [], $groupId = null): array
    {
        if (empty($userIds) && $groupId) {
            $userIds = $this->getUsersForCompetitionById($competitionId, $groupId)
                ->pluck('id')
                ->toArray();
        }

        $competition = $this->getCompetitionWithUserAnswers($competitionId, $userIds)->load('groups');

        $userStats = [];
        foreach ($competition->quizzes as $quiz) {
            $quizStats = $this->getUserStatsForQuiz($quiz, $userIds);
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

        $selectedGroupName = null;
        if ($groupId) {
            $selectedGroupName = optional($competition->groups->firstWhere('id', (int) $groupId))->name;
        }

        $baseFileName = trim($competition->name.($selectedGroupName ? ' - '.$selectedGroupName : ''));

        return compact(
            'competition',
            'userStats',
            'groupRankings',
            'selectedGroupName',
            'baseFileName',
            'userIds',
            'groupId'
        );
    }
}
