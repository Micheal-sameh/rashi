<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Http\Request;

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
            $user = User::where('name', 'like', "%{$search}%")
                ->orWhere('membership_code', 'like', "%{$search}%")
                ->first();

            if ($user) {
                // Get point history for this user
                $pointHistory = PointHistory::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Calculate totals
                foreach ($pointHistory as $history) {
                    if (! in_array($history->type, ['Bonus', 'Return', 'Quiz'])) {
                        $totalDebit += $history->amount;
                    } else {
                        $totalCredit += $history->amount;
                    }
                }
            }
        }

        return view('user-history.index', compact('user', 'pointHistory', 'totalDebit', 'totalCredit', 'search'));
    }
}
