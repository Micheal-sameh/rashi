<?php

namespace App\Services;

use App\Enums\BonusPenaltyStatus;
use App\Enums\BonusPenaltyType;
use App\Enums\OrderStatus;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class FamilyService
{
    public function __construct(
        protected UserRepository $userRepository,
    ) {}

    public function totalCount()
    {
        $this->userRepository->pagination = false;
        $users = $this->userRepository->index([]);
        $familyCodes = $users->map(function ($user) {
            if (preg_match('/^(E\d+C\d+F\d+)/', $user->membership_code, $matches)) {
                return $matches[1];
            }

            return null;
        })
            ->filter()
            ->unique()
            ->values();

        return count($familyCodes);
    }

    public function searchFamilies(string $search)
    {
        $users = $this->userRepository->searchFamilies($search);

        // Extract unique family codes
        $familyCodes = $users->map(function ($user) {
            if (preg_match('/^(E\d+C\d+F\d+)/', $user->membership_code, $matches)) {
                return $matches[1];
            }

            return null;
        })
            ->filter()
            ->unique()
            ->values();

        if ($familyCodes->isEmpty()) {
            return [
                'families' => [],
                'count' => $this->totalCount(),
            ];
        }

        // Get all family members
        $allMembers = $this->userRepository->getFamilyMembers($familyCodes->toArray());

        // Group members by family code
        $families = $allMembers->groupBy(function ($user) {
            if (preg_match('/^(E\d+C\d+F\d+)/', $user->membership_code, $matches)) {
                return $matches[1];
            }

            return null;
        })
            ->filter()
            ->map(fn ($members, $code) => [
                'code' => $code,
                'members' => $members,
            ])
            ->values()
            ->all();

        return [
            'families' => $families,
            'count' => $this->totalCount(),
        ];
    }

    public function getFamilyDetails(string $familyCode)
    {
        $members = $this->userRepository->getFamilyMembersWithGroups($familyCode);

        if ($members->isEmpty()) {
            return [
                'membersData' => [],
                'familyCode' => $familyCode,
            ];
        }

        $memberIds = $members->pluck('id')->all();
        $memberIdsStr = implode(',', $memberIds);

        // Get all aggregated data
        $aggregatedData = $this->getAggregatedMemberData($memberIdsStr);

        // Get quiz counts per group
        $groupQuizCounts = $this->getGroupQuizCounts($members);

        // Build member data
        $membersData = $members->map(function ($member) use ($aggregatedData, $groupQuizCounts) {
            $userId = $member->id;
            $userGroupIds = $member->groups->pluck('id')->all();

            // Calculate total quizzes for user's groups
            $totalQuizzes = collect($userGroupIds)
                ->sum(fn ($gid) => $groupQuizCounts[$gid] ?? 0);

            // Filter groups - exclude General
            $groups = $member->groups->filter(fn ($g) => $g->name !== 'General');

            return [
                'user' => $member,
                'final_score' => $member->score ?? 0,
                'final_points' => $member->points ?? 0,
                'quizzes_solved' => $aggregatedData['quizzes_solved'][$userId] ?? 0,
                'total_quizzes' => $totalQuizzes,
                'last_quiz' => $aggregatedData['last_quiz'][$userId] ?? null,
                'last_order' => $aggregatedData['last_order'][$userId] ?? null,
                'last_bonus' => $aggregatedData['last_bonus'][$userId] ?? null,
                'last_penalty' => $aggregatedData['last_penalty'][$userId] ?? null,
                'last_competition' => $aggregatedData['last_competition'][$userId] ?? null,
                'groups' => $groups,
            ];
        })->all();

        return [
            'membersData' => $membersData,
            'familyCode' => $familyCode,
        ];
    }

    /**
     * Get all aggregated member data in optimized queries
     */
    private function getAggregatedMemberData(string $memberIdsStr): array
    {
        // Quiz statistics
        $quizStats = DB::table('user_answers')
            ->whereRaw("user_id IN ($memberIdsStr)")
            ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
            ->select('user_id', DB::raw('COUNT(DISTINCT quiz_questions.quiz_id) as count'))
            ->groupBy('user_id')
            ->pluck('count', 'user_id');

        // Last quiz for each user (optimized with window function)
        $lastQuizzes = DB::select("
            WITH ranked_answers AS (
                SELECT
                    ua.user_id,
                    q.name,
                    ua.created_at as answer_created_at,
                    ROW_NUMBER() OVER (PARTITION BY ua.user_id ORDER BY ua.created_at DESC) as rn
                FROM user_answers ua
                INNER JOIN quiz_questions qq ON ua.quiz_question_id = qq.id
                INNER JOIN quizzes q ON qq.quiz_id = q.id
                WHERE ua.user_id IN ($memberIdsStr)
            )
            SELECT user_id, name, answer_created_at
            FROM ranked_answers
            WHERE rn = 1
        ");

        // Last order for each user
        $lastOrders = DB::select("
            WITH ranked_orders AS (
                SELECT
                    o.user_id,
                    r.name as reward_name,
                    o.created_at,
                    ROW_NUMBER() OVER (PARTITION BY o.user_id ORDER BY o.id DESC) as rn
                FROM orders o
                LEFT JOIN rewards r ON o.reward_id = r.id
                WHERE o.user_id IN ($memberIdsStr)
                  AND o.status = ?
            )
            SELECT user_id, reward_name, created_at
            FROM ranked_orders
            WHERE rn = 1
        ", [OrderStatus::COMPLETED]);

        // Last bonus and penalty (combined query)
        $bonusPenaltyData = DB::select("
            WITH ranked_bp AS (
                SELECT
                    user_id,
                    points,
                    created_at,
                    type,
                    ROW_NUMBER() OVER (PARTITION BY user_id, type ORDER BY id DESC) as rn
                FROM bonuses_penalties
                WHERE user_id IN ($memberIdsStr)
                  AND status = ?
                  AND type IN (?, ?)
            )
            SELECT user_id, points, created_at, type
            FROM ranked_bp
            WHERE rn = 1
        ", [
            BonusPenaltyStatus::APPLIED,
            BonusPenaltyType::BONUS,
            BonusPenaltyType::PENALTY,
        ]);

        // Last competition for each user
        $lastCompetitions = DB::select("
            WITH ranked_comp AS (
                SELECT
                    ua.user_id,
                    c.name,
                    ua.created_at as answer_created_at,
                    ROW_NUMBER() OVER (PARTITION BY ua.user_id ORDER BY ua.id DESC) as rn
                FROM user_answers ua
                INNER JOIN quiz_questions qq ON ua.quiz_question_id = qq.id
                INNER JOIN quizzes q ON qq.quiz_id = q.id
                INNER JOIN competitions c ON q.competition_id = c.id
                WHERE ua.user_id IN ($memberIdsStr)
            )
            SELECT user_id, name, answer_created_at
            FROM ranked_comp
            WHERE rn = 1
        ");

        // Format results
        return [
            'quizzes_solved' => $quizStats,
            'last_quiz' => collect($lastQuizzes)->mapWithKeys(fn ($q) => [
                $q->user_id => [
                    'name' => $q->name ?? 'N/A',
                    'date' => $q->answer_created_at ?? null,
                ],
            ]),
            'last_order' => collect($lastOrders)->mapWithKeys(fn ($o) => [
                $o->user_id => [
                    'reward' => $o->reward_name ?? 'N/A',
                    'date' => $o->created_at,
                ],
            ]),
            'last_bonus' => collect($bonusPenaltyData)
                ->where('type', BonusPenaltyType::BONUS)
                ->mapWithKeys(fn ($b) => [
                    $b->user_id => [
                        'value' => $b->points,
                        'date' => $b->created_at,
                    ],
                ]),
            'last_penalty' => collect($bonusPenaltyData)
                ->where('type', BonusPenaltyType::PENALTY)
                ->mapWithKeys(fn ($p) => [
                    $p->user_id => [
                        'value' => $p->points,
                        'date' => $p->created_at,
                    ],
                ]),
            'last_competition' => collect($lastCompetitions)->mapWithKeys(fn ($c) => [
                $c->user_id => [
                    'name' => $c->name ?? 'N/A',
                    'date' => $c->answer_created_at ?? null,
                ],
            ]),
        ];
    }

    /**
     * Get quiz counts per group efficiently
     */
    private function getGroupQuizCounts($members): array
    {
        $allGroupIds = $members->flatMap(fn ($m) => $m->groups->pluck('id'))
            ->unique()
            ->values()
            ->all();

        if (empty($allGroupIds)) {
            return [];
        }

        return DB::table('quizzes')
            ->join('competitions', 'quizzes.competition_id', '=', 'competitions.id')
            ->join('competition_groups', 'competitions.id', '=', 'competition_groups.competition_id')
            ->whereIn('competition_groups.group_id', $allGroupIds)
            ->select('competition_groups.group_id', DB::raw('COUNT(DISTINCT quizzes.id) as count'))
            ->groupBy('competition_groups.group_id')
            ->pluck('count', 'group_id')
            ->all();
    }
}
