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
                    ->get();

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
        // Get all family members
        $members = User::where('membership_code', 'like', $familyCode.'%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(membership_code, 'NR', -1) AS UNSIGNED)")
            ->get();

        // Get detailed information for each member
        $membersData = [];
        foreach ($members as $member) {
            // Get quiz statistics
            $totalQuizzes = Quiz::count();
            $solvedQuizzes = DB::table('user_quizzes')
                ->where('user_id', $member->id)
                ->distinct('quiz_id')
                ->count('quiz_id');

            $lastQuiz = DB::table('user_quizzes')
                ->where('user_id', $member->id)
                ->join('quizzes', 'user_quizzes.quiz_id', '=', 'quizzes.id')
                ->orderBy('user_quizzes.created_at', 'desc')
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

            // Get last competition
            $lastCompetition = DB::table('competition_user')
                ->where('user_id', $member->id)
                ->join('competitions', 'competition_user.competition_id', '=', 'competitions.id')
                ->orderBy('competition_user.created_at', 'desc')
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
                    'name' => $lastQuiz->title ?? $lastQuiz->name ?? 'N/A',
                    'date' => $lastQuiz->created_at ?? null,
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
                    'date' => $lastCompetition->created_at ?? null,
                ] : null,
                'groups' => $groups,
            ];
        }

        return view('families.show', compact('membersData', 'familyCode'));
    }
}
