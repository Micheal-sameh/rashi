<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PointTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointTransferController extends Controller
{
    public function __construct(protected PointTransferService $pointTransferService) {}

    /**
     * Get user's transfer history
     */
    public function myTransfers(Request $request)
    {
        $userId = Auth::id();

        $filters = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        $transfers = $this->pointTransferService->getUserTransfers($userId, $filters);

        return response()->json([
            'success' => true,
            'data' => $transfers,
        ]);
    }

    /**
     * Get family members
     */
    public function getFamilyMembers(Request $request)
    {
        $user = Auth::user();

        // Extract family code from user's membership code
        $familyCode = \App\Models\PointTransfer::extractFamilyCode($user->membership_code);

        if (! $familyCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid membership code format.',
            ], 400);
        }

        $familyMembers = $this->pointTransferService->getFamilyMembers($familyCode);

        return response()->json([
            'success' => true,
            'data' => $familyMembers,
            'family_code' => $familyCode,
        ]);
    }

    /**
     * Request a transfer (if you want users to initiate transfers)
     */
    public function requestTransfer(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id|different:'.Auth::id(),
            'points' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        // For regular users, they can only send from their own account
        $validated['sender_id'] = Auth::id();

        try {
            $transfer = $this->pointTransferService->transferPoints($validated);

            return response()->json([
                'success' => true,
                'message' => 'Points transferred successfully.',
                'data' => $transfer,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get transfer details
     */
    public function show($id)
    {
        try {
            $transfer = $this->pointTransferService->show($id);

            // Check authorization
            $user = Auth::user();
            if ($transfer->sender_id !== $user->id &&
                $transfer->receiver_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $transfer,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer not found.',
            ], 404);
        }
    }

    /**
     * Validate transfer before submission
     */
    public function validateTransfer(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
        ]);

        $validation = $this->pointTransferService->validateTransfer(
            Auth::id(),
            $request->receiver_id,
            $request->points
        );

        return response()->json($validation);
    }
}
