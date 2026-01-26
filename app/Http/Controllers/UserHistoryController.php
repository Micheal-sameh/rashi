<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = null;
        $pointHistory = collect();
        $totalDebit = 0;
        $totalCredit = 0;
        $search = $request->search;

        if ($search) {
            // Search for user by name or membership_code
            $user = User::where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('membership_code', 'like', "%{$search}%");
            })
                ->select('id', 'name', 'membership_code', 'points', 'score')
                ->first();

            if ($user) {
                // Calculate totals using database aggregation for better performance
                $creditTypes = ['App\\\\Models\\\\BonusPenalty', 'App\\\\Models\\\\Quiz', 'App\\\\Models\\\\Returns'];

                $totals = DB::table('point_histories')
                    ->where('user_id', $user->id)
                    ->selectRaw('
                        SUM(CASE
                            WHEN subject_type IN ("'.implode('","', $creditTypes).'")
                            THEN amount
                            ELSE 0
                        END) as total_credit,
                        SUM(CASE
                            WHEN subject_type NOT IN ("'.implode('","', $creditTypes).'")
                            THEN amount
                            ELSE 0
                        END) as total_debit
                    ')
                    ->first();

                $totalCredit = $totals->total_credit ?? 0;
                $totalDebit = $totals->total_debit ?? 0;

                // Get point history with limited columns
                $pointHistory = PointHistory::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'user_id', 'subject_type', 'subject_id', 'amount', 'created_at']);
            }
        }

        return view('user-history.index', compact('user', 'pointHistory', 'totalDebit', 'totalCredit', 'search'));
    }
}
