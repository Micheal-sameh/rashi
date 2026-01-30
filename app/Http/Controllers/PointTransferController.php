<?php

namespace App\Http\Controllers;

use App\Services\PointTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointTransferController extends Controller
{
    public function __construct(protected PointTransferService $pointTransferService) {}

    /**
     * Display list of all point transfers
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $filters = [
            'search' => $request->search,
            'family_code' => $request->family_code,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        $transfers = $this->pointTransferService->index($filters);

        return view('point-transfers.index', compact('transfers'));
    }

    /**
     * Display transfers for current user
     */
    public function myTransfers(Request $request)
    {
        $userId = Auth::id();

        $filters = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        $transfers = $this->pointTransferService->getUserTransfers($userId, $filters);

        return view('point-transfers.my-transfers', compact('transfers'));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $familyMembers = null;

        // If family_code is provided, get family members
        if ($request->filled('family_code')) {
            $familyMembers = $this->pointTransferService->getFamilyMembers($request->family_code);
        }

        return view('point-transfers.create', compact('familyMembers'));
    }

    /**
     * Get family members for AJAX request
     */
    public function getFamilyMembers(Request $request)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'family_code' => 'required|string',
        ]);

        $familyMembers = $this->pointTransferService->getFamilyMembers($request->family_code);

        return response()->json([
            'success' => true,
            'members' => $familyMembers,
        ]);
    }

    /**
     * Store a new point transfer
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id|different:sender_id',
            'points' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $transfer = $this->pointTransferService->transferPoints($validated);

            return redirect()
                ->route('point-transfers.index')
                ->with('success', 'Points transferred successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display a single transfer
     */
    public function show($id)
    {
        $transfer = $this->pointTransferService->show($id);

        // Check authorization: admin or involved user
        $user = Auth::user();
        if (! $user->hasRole('admin') &&
            $transfer->sender_id !== $user->id &&
            $transfer->receiver_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('point-transfers.show', compact('transfer'));
    }

    /**
     * Validate transfer (AJAX)
     */
    public function validateTransfer(Request $request)
    {
        // Check if user is admin
        if (! Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
        ]);

        $validation = $this->pointTransferService->validateTransfer(
            $request->sender_id,
            $request->receiver_id,
            $request->points
        );

        return response()->json($validation);
    }
}
