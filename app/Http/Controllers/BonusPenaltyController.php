<?php

namespace App\Http\Controllers;

use App\Enums\BonusPenaltyStatus;
use App\Models\BonusPenalty;
use App\Services\BonusPenaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BonusPenaltyController extends Controller
{
    public function __construct(protected BonusPenaltyService $bonusPenaltyService) {}

    /**
     * Display applied bonus/penalties
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'created_by' => $request->created_by,
            'user_id' => $request->user_id,
            'type' => $request->type,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        $bonusPenalties = $this->bonusPenaltyService->getApplied($filters);

        return view('bonus-penalties.index', compact('bonusPenalties'));
    }

    /**
     * Display pending bonus/penalties for approval
     */
    public function pendingList(Request $request)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $filters = [
            'search' => $request->search,
            'created_by' => $request->created_by,
        ];

        $bonusPenalties = $this->bonusPenaltyService->getPending($filters);

        return view('bonus-penalties.pending', compact('bonusPenalties'));
    }

    /**
     * Approve a bonus/penalty
     */
    public function approve($id)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $bonusPenalty = BonusPenalty::findOrFail($id);

        if ($bonusPenalty->status == BonusPenaltyStatus::APPLIED) {
            return redirect()->back()->with('error', 'This bonus/penalty has already been approved.');
        }

        $this->bonusPenaltyService->approve($bonusPenalty);

        return redirect()->back()->with('success', 'Bonus/Penalty approved successfully.');
    }

    /**
     * Reject a bonus/penalty
     */
    public function reject($id)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $bonusPenalty = BonusPenalty::findOrFail($id);

        if ($bonusPenalty->status == BonusPenaltyStatus::APPLIED) {
            return redirect()->back()->with('error', 'This bonus/penalty has already been approved and cannot be rejected.');
        }

        $bonusPenalty->delete();

        return redirect()->back()->with('success', 'Bonus/Penalty rejected and deleted successfully.');
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('bonus-penalties.create');
    }

    /**
     * Store a new bonus/penalty
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:1,2',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $this->bonusPenaltyService->store($validated);

        return redirect()->route('bonus-penalties.index')->with('success', 'Bonus/Penalty created successfully');
    }

    /**
     * Show a single bonus/penalty
     */
    public function show($id)
    {
        $bonusPenalty = $this->bonusPenaltyService->show($id);

        return view('bonus-penalties.show', compact('bonusPenalty'));
    }
}
