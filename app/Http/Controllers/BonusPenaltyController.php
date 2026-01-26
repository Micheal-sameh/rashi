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

    public function index(Request $request)
    {
        $query = BonusPenalty::with(['user', 'creator', 'approver'])
            ->where('status', BonusPenaltyStatus::APPLIED);

        // Search by name or membership_code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('membership_code', 'like', "%{$search}%");
            });
        }

        // Filter by created_by
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $bonusPenalties = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('bonus-penalties.index', compact('bonusPenalties'));
    }

    public function pendingList(Request $request)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $query = BonusPenalty::with(['user', 'creator'])
            ->where('status', BonusPenaltyStatus::PENDING_APPROVAL);

        // Search by name or membership_code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('membership_code', 'like', "%{$search}%");
            });
        }

        // Filter by created_by
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $bonusPenalties = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('bonus-penalties.pending', compact('bonusPenalties'));
    }

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

    public function create()
    {
        return view('bonus-penalties.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:1,2',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $this->bonusPenaltyService->store($request->all());

        return redirect()->route('bonus-penalties.index')->with('success', 'Bonus/Penalty created successfully');
    }

    public function show($id)
    {
        $bonusPenalty = $this->bonusPenaltyService->show($id);

        return view('bonus-penalties.show', compact('bonusPenalty'));
    }
}
