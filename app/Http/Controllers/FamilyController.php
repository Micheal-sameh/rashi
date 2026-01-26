<?php

namespace App\Http\Controllers;

use App\Models\BonusPenalty;
use App\Models\Competition;
use App\Models\Order;
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
            ->with('groups')
            ->orderByRaw("CAST(SUBSTRING_INDEX(membership_code, 'NR', -1) AS UNSIGNED)")
            ->get(['id', 'name', 'membership_code', 'points', 'score']);

        // Get detailed information for each member
        $membersData = [];
        foreach ($members as $member) {
            // Get quiz statistics - filter by user's groups
            $userGroupIds = $member->groups->pluck('id')->toArray();

            $totalQuizzes = Quiz::query()
                ->whereHas('competition', function ($query) use ($userGroupIds) {
                    $query->whereHas('groups', function ($q) use ($userGroupIds) {
                        $q->whereIn('groups.id', $userGroupIds);
                    });
                })->count();
            $solvedQuizzes = DB::table('user_answers')
                ->where('user_id', $member->id)
                ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                ->distinct('quiz_questions.quiz_id')
                ->count('quiz_questions.quiz_id');

            $lastQuiz = DB::table('user_answers')
                ->where('user_id', $member->id)
                ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                ->join('quizzes', 'quiz_questions.quiz_id', '=', 'quizzes.id')
                ->orderBy('user_answers.created_at', 'desc')
                ->select('quizzes.*', 'user_answers.created_at as answer_created_at')
                ->first();

            // Get last redeem (order)
            $lastOrder = Order::where('user_id', $member->id)
                ->where('status', \App\Enums\OrderStatus::COMPLETED)
                ->with('reward')
                ->orderBy('created_at', 'desc')
                ->first();

            // Get last bonus
            $lastBonus = BonusPenalty::where('user_id', $member->id)
                ->where('type', \App\Enums\BonusPenaltyType::BONUS)
                ->where('status', \App\Enums\BonusPenaltyStatus::APPLIED)
                ->orderBy('created_at', 'desc')
                ->first();

            // Get last penalty
            $lastPenalty = BonusPenalty::where('user_id', $member->id)
                ->where('type', \App\Enums\BonusPenaltyType::PENALTY)
                ->where('status', \App\Enums\BonusPenaltyStatus::APPLIED)
                ->orderBy('created_at', 'desc')
                ->first();

            // Get last competition (through user_answers -> quiz_questions -> quizzes -> competitions)
            $lastCompetition = DB::table('user_answers')
                ->where('user_answers.user_id', $member->id)
                ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                ->join('quizzes', 'quiz_questions.quiz_id', '=', 'quizzes.id')
                ->join('competitions', 'quizzes.competition_id', '=', 'competitions.id')
                ->orderBy('user_answers.created_at', 'desc')
                ->select('competitions.*', 'user_answers.created_at as answer_created_at')
                ->first();

            // Get groups except General
            $groups = $member->groups()->where('name', '!=', 'General')->get();

            $membersData[] = [
                'user' => $member,
                'final_score' => $member->score ?? 0,
                'final_points' => $member->points ?? 0,
                'quizzes_solved' => $solvedQuizzes,
                'total_quizzes' => $totalQuizzes,
                'last_quiz' => $lastQuiz ? [
                    'name' => $lastQuiz->name ?? 'N/A',
                    'date' => $lastQuiz->answer_created_at ?? null,
                ] : null,
                'last_order' => $lastOrder ? [
                    'reward' => $lastOrder->reward->name ?? 'N/A',
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
