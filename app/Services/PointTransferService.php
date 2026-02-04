<?php

namespace App\Services;

use App\Jobs\SendPointTransferNotification;
use App\Models\PointHistory;
use App\Models\PointTransfer;
use App\Repositories\PointTransferRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PointTransferService
{
    public function __construct(
        protected PointTransferRepository $pointTransferRepository,
        protected UserRepository $userRepository,
    ) {}

    /**
     * Get all point transfers with filters
     */
    public function index(array $filters = [])
    {
        return $this->pointTransferRepository->index($filters);
    }

    /**
     * Get transfers for a specific user
     */
    public function getUserTransfers(int $userId, array $filters = [])
    {
        return $this->pointTransferRepository->getUserTransfers($userId, $filters);
    }

    /**
     * Get transfers for a specific family
     */
    public function getFamilyTransfers(string $familyCode, array $filters = [])
    {
        return $this->pointTransferRepository->getFamilyTransfers($familyCode, $filters);
    }

    /**
     * Get family members for dropdown
     */
    public function getFamilyMembers(string $familyCode)
    {
        return $this->pointTransferRepository->getFamilyMembers($familyCode);
    }

    /**
     * Transfer points between family members
     *
     * @param  array  $data  ['sender_id', 'receiver_id', 'points', 'reason']
     * @return PointTransfer
     *
     * @throws \Exception
     */
    public function transferPoints(array $data)
    {
        try {
            DB::beginTransaction();

            // Get sender and receiver
            $sender = $this->userRepository->findById($data['sender_id']);
            $receiver = $this->userRepository->findById($data['receiver_id']);

            // Validate: Both must be from same family
            $senderFamilyCode = PointTransfer::extractFamilyCode($sender->membership_code);
            $receiverFamilyCode = PointTransfer::extractFamilyCode($receiver->membership_code);

            if (! $senderFamilyCode || ! $receiverFamilyCode) {
                throw new \Exception('Invalid membership code format.');
            }

            if ($senderFamilyCode !== $receiverFamilyCode) {
                throw new \Exception('Users must be from the same family to transfer points.');
            }

            // Calculate 10% fee
            $transferFee = (int) ceil($data['points'] * 0.10);
            $totalDeduction = $data['points'] + $transferFee;

            // Validate: Sender has enough points (including fee)
            if ($sender->points < $totalDeduction) {
                throw new \Exception("Sender does not have enough points. Transfer requires {$totalDeduction} points ({$data['points']} transfer + {$transferFee} fee).");
            }

            // Validate: 30% limit of points gained in last month
            $transferLimit = $this->getMaxTransferablePoints($sender->id);

            if ($data['points'] > ($transferLimit['max_transferable'] * 0.3)) {
                throw new \Exception("You can only transfer up to 30% of points gained in the last month. Maximum allowed: {$transferLimit['max_transferable']} points (30% of {$transferLimit['points_gained_last_month']} points gained).");
            }

            // Validate: Cannot transfer to self
            if ($sender->id === $receiver->id) {
                throw new \Exception('Cannot transfer points to yourself.');
            }

            // Create the transfer record
            $transfer = $this->pointTransferRepository->store([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'points' => $data['points'],
                'family_code' => $senderFamilyCode,
                'reason' => $data['reason'] ?? 'Points Transfer',
                'created_by' => auth()->id() ?? 1,
            ]);

            // Deduct points from sender (transfer amount + 10% fee)
            $sender->update([
                'points' => $sender->points - $totalDeduction,
            ]);

            // Add points to receiver (only the transfer amount, not the fee)
            $receiver->update([
                'points' => $receiver->points + $data['points'],
            ]);

            // Create point history for sender (transfer deduction)
            $this->createPointHistory([
                'user_id' => $sender->id,
                'amount' => $data['points'],
                'points' => $sender->points,
                'score' => $sender->score,
                'subject_id' => $transfer->id,
                'subject_type' => PointTransfer::class,
                'type' => 'deduction',
            ]);

            // Create point history for sender (10% transfer fee)
            if ($transferFee > 0) {
                $this->createPointHistory([
                    'user_id' => $sender->id,
                    'amount' => $transferFee,
                    'points' => $sender->points,
                    'score' => $sender->score,
                    'subject_id' => $transfer->id,
                    'subject_type' => PointTransfer::class,
                    'type' => 'deduction',
                ]);
            }

            // Create point history for receiver (addition)
            $this->createPointHistory([
                'user_id' => $receiver->id,
                'amount' => $data['points'],
                'points' => $receiver->points,
                'score' => $receiver->score,
                'subject_id' => $transfer->id,
                'subject_type' => PointTransfer::class,
                'type' => 'addition',
            ]);

            DB::commit();

            // Load relationships for response
            $transfer->load(['sender', 'receiver', 'creator']);

            // Dispatch job to send notifications asynchronously
            SendPointTransferNotification::dispatch($transfer, $sender->id, $receiver->id);

            return $transfer;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Point transfer failed: '.$e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get single transfer by ID
     */
    public function show($id)
    {
        return $this->pointTransferRepository->findByIdWithRelations($id);
    }

    /**
     * Create point history record
     */
    protected function createPointHistory(array $data)
    {
        return PointHistory::create([
            'user_id' => $data['user_id'],
            'amount' => $data['amount'],
            'points' => $data['points'],
            'score' => $data['score'],
            'subject_id' => $data['subject_id'],
            'subject_type' => $data['subject_type'],
        ]);
    }

    /**
     * Calculate points gained by user in the last month
     */
    protected function getPointsGainedLastMonth(int $userId): int
    {
        $startOneMonthAgo = now()->subMonth()->startOfMonth();
        $endneMonthAgo = now()->subMonth()->endOfMonth();

        // Sum all positive point additions in the last month
        $pointsGained = PointHistory::where('user_id', $userId)
            ->whereBetween('created_at', [$startOneMonthAgo, $endneMonthAgo])
            ->whereIn('subject_type', [
                'App\\Models\\Quiz',
                'App\\Models\\BonusPenalty',
            ])
            ->where('amount', '>', 0)
            ->sum('amount');

        // Also add points received from transfers
        // Use a join to avoid polymorphic whereHas issues
        $transfersReceived = PointHistory::where('point_histories.user_id', $userId)
            ->where('point_histories.subject_type', PointTransfer::class)
            ->whereBetween('point_histories.created_at', [$startOneMonthAgo, $endneMonthAgo])
            ->join('point_transfers', function ($join) use ($userId) {
                $join->on('point_histories.subject_id', '=', 'point_transfers.id')
                    ->where('point_transfers.receiver_id', '=', $userId);
            })
            ->sum('point_histories.amount');

        return (int) ($pointsGained + $transfersReceived);
    }

    /**
     * Calculate maximum transferable points (30% of last month's gains)
     */
    public function getMaxTransferablePoints(int $userId): array
    {
        $pointsGainedLastMonth = $this->getPointsGainedLastMonth($userId);
        $maxTransferable = (int) floor($pointsGainedLastMonth * 0.30);

        return [
            'points_gained_last_month' => $pointsGainedLastMonth,
            'max_transferable' => $maxTransferable,
        ];
    }

    /**
     * Validate transfer request
     */
    public function validateTransfer(int $senderId, int $receiverId, int $points): array
    {
        $errors = [];

        try {
            $sender = $this->userRepository->findById($senderId);
            $receiver = $this->userRepository->findById($receiverId);

            // Check family codes
            $senderFamilyCode = PointTransfer::extractFamilyCode($sender->membership_code);
            $receiverFamilyCode = PointTransfer::extractFamilyCode($receiver->membership_code);

            if (! $senderFamilyCode || ! $receiverFamilyCode) {
                $errors[] = 'Invalid membership code format.';
            } elseif ($senderFamilyCode != $receiverFamilyCode) {
                $errors[] = 'Users must be from the same family.';
            }

            // Check points
            if ($sender->points < $points) {
                $errors[] = 'Sender does not have enough points.';
            }

            // Check 30% limit of points gained in last month
            $transferLimit = $this->getMaxTransferablePoints($senderId);
            if ($points > $transferLimit['max_transferable']) {
                $errors[] = "You can only transfer up to 30% of points gained in the last month. Maximum allowed: {$transferLimit['max_transferable']} points (30% of {$transferLimit['points_gained_last_month']} points gained).";
            }

            // Check not same user
            if ($sender->id === $receiver->id) {
                $errors[] = 'Cannot transfer points to yourself.';
            }

            return [
                'valid' => empty($errors),
                'errors' => $errors,
                'sender' => $sender,
                'receiver' => $receiver,
                'transfer_limit' => $transferLimit,
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }
}
