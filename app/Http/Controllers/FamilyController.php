<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FamilyController extends Controller
{
    public function index(Request $request)
    {
        $families = [];
        $search = $request->search;

        if ($search) {
            // Get all users matching the search (by name or membership_code)
            $users = User::where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('membership_code', 'like', "%{$search}%");
            })->get();

            // Extract family codes from users (E1C1Fxxx format)
            $familyCodes = [];
            foreach ($users as $user) {
                if (preg_match('/^(E\d+C\d+F\d+)/', $user->membership_code, $matches)) {
                    $familyCodes[] = $matches[1];
                }
            }

            // Get unique family codes
            $familyCodes = array_unique($familyCodes);

            // For each family code, get all users and their NR number
            foreach ($familyCodes as $familyCode) {
                $familyMembers = User::where('membership_code', 'like', $familyCode.'%')
                    ->orderByRaw("CAST(SUBSTRING_INDEX(membership_code, 'NR', -1) AS UNSIGNED)")
                    ->get(['id', 'name', 'membership_code']);

                if ($familyMembers->isNotEmpty()) {
                    $families[] = [
                        'code' => $familyCode,
                        'members' => $familyMembers,
                    ];
                }
            }
        }

        return view('families.index', compact('families', 'search'));
    }

    public function show($familyCode)
    {
        // Get all family members with their groups in one query
        $members = User::where('membership_code', 'like', $familyCode.'%')
            ->with('groups:id,name')
            ->orderByRaw("CAST(SUBSTRING_INDEX(membership_code, 'NR', -1) AS UNSIGNED)")
            ->get(['id', 'name', 'membership_code', 'points', 'score']);

        if ($members->isEmpty()) {
            return view('families.show', ['membersData' => [], 'familyCode' => $familyCode]);
        }

        $memberIds = $members->pluck('id')->toArray();

        // Collect all unique group combinations for quiz count caching
        $allGroupIds = $members->flatMap(fn ($m) => $m->groups->pluck('id'))->unique()->values()->toArray();

        // Pre-calculate quiz counts per group set to avoid N queries
        $quizCountCache = [];
        if (! empty($allGroupIds)) {
            // Get quiz counts for relevant group combinations in one query
            $groupQuizCounts = DB::table('quizzes')
                ->join('competitions', 'quizzes.competition_id', '=', 'competitions.id')
                ->join('competition_groups', 'competitions.id', '=', 'competition_groups.competition_id')
                ->whereIn('competition_groups.group_id', $allGroupIds)
                ->select('competition_groups.group_id', DB::raw('COUNT(DISTINCT quizzes.id) as count'))
                ->groupBy('competition_groups.group_id')
                ->pluck('count', 'group_id');
        }

        // Batch fetch all quiz statistics for all members at once
        $quizStats = DB::table('user_answers')
            ->whereIn('user_id', $memberIds)
            ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
            ->select('user_id', DB::raw('COUNT(DISTINCT quiz_questions.quiz_id) as count'))
            ->groupBy('user_id')
            ->pluck('count', 'user_id');

        // Optimize: Use window function to get last records efficiently
        $lastQuizzes = DB::select('
            SELECT ua.user_id, q.name, ua.created_at as answer_created_at
            FROM user_answers ua
            INNER JOIN quiz_questions qq ON ua.quiz_question_id = qq.id
            INNER JOIN quizzes q ON qq.quiz_id = q.id
            INNER JOIN (
                SELECT user_id, MAX(created_at) as max_created
                FROM user_answers
                WHERE user_id IN ('.implode(',', $memberIds).')
                GROUP BY user_id
            ) latest ON ua.user_id = latest.user_id AND ua.created_at = latest.max_created
        ');
        $lastQuizzes = collect($lastQuizzes)->keyBy('user_id');

        // Optimize last orders with better subquery
        $lastOrders = DB::select('
            SELECT o.user_id, r.name as reward_name, o.created_at
            FROM orders o
            LEFT JOIN rewards r ON o.reward_id = r.id
            INNER JOIN (
                SELECT user_id, MAX(id) as max_id
                FROM orders
                WHERE user_id IN ('.implode(',', $memberIds).")
                  AND status = '".\App\Enums\OrderStatus::COMPLETED->value."'
                GROUP BY user_id
            ) latest ON o.id = latest.max_id
        ");
        $lastOrders = collect($lastOrders)->keyBy('user_id');

        // Optimize bonus/penalty queries with UNION ALL
        $bonusPenaltyData = DB::select("
            SELECT
                user_id,
                points,
                created_at,
                'bonus' as bp_type
            FROM bonus_penalties
            WHERE user_id IN (".implode(',', $memberIds).")
              AND type = '".\App\Enums\BonusPenaltyType::BONUS->value."'
              AND status = '".\App\Enums\BonusPenaltyStatus::APPLIED->value."'
              AND id IN (
                SELECT MAX(id)
                FROM bonus_penalties
                WHERE user_id IN (".implode(',', $memberIds).")
                  AND type = '".\App\Enums\BonusPenaltyType::BONUS->value."'
                  AND status = '".\App\Enums\BonusPenaltyStatus::APPLIED->value."'
                GROUP BY user_id
              )
            UNION ALL
            SELECT
                user_id,
                points,
                created_at,
                'penalty' as bp_type
            FROM bonus_penalties
            WHERE user_id IN (".implode(',', $memberIds).")
              AND type = '".\App\Enums\BonusPenaltyType::PENALTY->value."'
              AND status = '".\App\Enums\BonusPenaltyStatus::APPLIED->value."'
              AND id IN (
                SELECT MAX(id)
                FROM bonus_penalties
                WHERE user_id IN (".implode(',', $memberIds).")
                  AND type = '".\App\Enums\BonusPenaltyType::PENALTY->value."'
                  AND status = '".\App\Enums\BonusPenaltyStatus::APPLIED->value."'
                GROUP BY user_id
              )
        ");

        $lastBonuses = collect($bonusPenaltyData)->where('bp_type', 'bonus')->keyBy('user_id');
        $lastPenalties = collect($bonusPenaltyData)->where('bp_type', 'penalty')->keyBy('user_id');

        // Optimize last competitions
        $lastCompetitions = DB::select('
            SELECT ua.user_id, c.name, ua.created_at as answer_created_at
            FROM user_answers ua
            INNER JOIN quiz_questions qq ON ua.quiz_question_id = qq.id
            INNER JOIN quizzes q ON qq.quiz_id = q.id
            INNER JOIN competitions c ON q.competition_id = c.id
            INNER JOIN (
                SELECT user_id, MAX(id) as max_id
                FROM user_answers
                WHERE user_id IN ('.implode(',', $memberIds).')
                GROUP BY user_id
            ) latest ON ua.id = latest.max_id
        ');
        $lastCompetitions = collect($lastCompetitions)->keyBy('user_id');

        // Build member data using pre-fetched data
        $membersData = [];
        foreach ($members as $member) {
            $userGroupIds = $member->groups->pluck('id')->toArray();

            // Use cached quiz counts
            $totalQuizzes = 0;
            if (isset($groupQuizCounts)) {
                foreach ($userGroupIds as $groupId) {
                    $totalQuizzes += $groupQuizCounts->get($groupId, 0);
                }
            }

            $lastQuiz = $lastQuizzes->get($member->id);
            $lastOrder = $lastOrders->get($member->id);
            $lastBonus = $lastBonuses->get($member->id);
            $lastPenalty = $lastPenalties->get($member->id);
            $lastCompetition = $lastCompetitions->get($member->id);

            // Filter groups - exclude General (already loaded)
            $groups = $member->groups->filter(fn ($g) => $g->name !== 'General');

            $membersData[] = [
                'user' => $member,
                'final_score' => $member->score ?? 0,
                'final_points' => $member->points ?? 0,
                'quizzes_solved' => $quizStats->get($member->id, 0),
                'total_quizzes' => $totalQuizzes,
                'last_quiz' => $lastQuiz ? [
                    'name' => $lastQuiz->name ?? 'N/A',
                    'date' => $lastQuiz->answer_created_at ?? null,
                ] : null,
                'last_order' => $lastOrder ? [
                    'reward' => $lastOrder->reward_name ?? 'N/A',
                    'date' => $lastOrder->created_at,
                ] : null,
                'last_bonus' => $lastBonus ? [
                    'value' => $lastBonus->points,
                    'date' => $lastBonus->created_at,
                ] : null,
                'last_penalty' => $lastPenalty ? [
                    'value' => $lastPenalty->points,
                    'date' => $lastPenalty->created_at,
                ] : null,
                'last_competition' => $lastCompetition ? [
                    'name' => $lastCompetition->name ?? 'N/A',
                    'date' => $lastCompetition->answer_created_at ?? null,
                ] : null,
                'groups' => $groups,
            ];
        }

        return view('families.show', compact('membersData', 'familyCode'));
    }
}
