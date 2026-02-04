<?php

namespace App\Services;

use App\Enums\BonusPenaltyType;
use App\Jobs\SendPointTransferNotification;
use App\Models\PointHistory;
use App\Models\PointTransfer;
use App\Models\User;
use App\Repositories\PointTransferRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PointTransferService
{
    private const TRANSFER_FEE_PERCENTAGE = 0.10;

    private const MAX_TRANSFER_PERCENTAGE = 0.30;

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
     *
     * @throws \Exception
     */
    public function transferPoints(array $data): PointTransfer
    {
        try {
            DB::beginTransaction();

            $sender = $this->userRepository->findById($data['sender_id']);
            $receiver = $this->userRepository->findById($data['receiver_id']);

            $this->validateFamilyMembership($sender, $receiver);

            $transferFee = $this->calculateTransferFee($data['points']);
            $totalDeduction = $data['points'] + $transferFee;

            $this->validateSufficientPoints($sender, $totalDeduction, $data['points'], $transferFee);
            $this->validateTransferLimit($sender->id, $data['points']);
            $this->validateNotSelfTransfer($sender->id, $receiver->id);

            $familyCode = PointTransfer::extractFamilyCode($sender->membership_code);

            $transfer = $this->createTransferRecord($sender, $receiver, $data, $familyCode);
            $this->updateUserPoints($sender, $receiver, $data['points'], $totalDeduction);
            $this->recordPointHistories($sender, $receiver, $transfer, $data['points'], $transferFee);

            DB::commit();

            $transfer->load(['sender', 'receiver', 'creator']);
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
     * Validate family membership between sender and receiver
     *
     * @throws \Exception
     */
    protected function validateFamilyMembership(User $sender, User $receiver): void
    {
        $senderFamilyCode = PointTransfer::extractFamilyCode($sender->membership_code);
        $receiverFamilyCode = PointTransfer::extractFamilyCode($receiver->membership_code);

        if (! $senderFamilyCode || ! $receiverFamilyCode) {
            throw new \Exception('Invalid membership code format.');
        }

        if ($senderFamilyCode !== $receiverFamilyCode) {
            throw new \Exception('Users must be from the same family to transfer points.');
        }
    }

    /**
     * Calculate transfer fee (10%)
     */
    protected function calculateTransferFee(int $points): int
    {
        return (int) ceil($points * self::TRANSFER_FEE_PERCENTAGE);
    }

    /**
     * Validate sender has sufficient points
     *
     * @throws \Exception
     */
    protected function validateSufficientPoints(User $sender, int $totalDeduction, int $points, int $fee): void
    {
        if ($sender->points < $totalDeduction) {
            throw new \Exception("Sender does not have enough points. Transfer requires {$totalDeduction} points ({$points} transfer + {$fee} fee).");
        }
    }

    /**
     * Validate transfer does not exceed 30% limit
     *
     * @throws \Exception
     */
    protected function validateTransferLimit(int $senderId, int $points): void
    {
        $transferLimit = $this->getMaxTransferablePoints($senderId);

        if ($points > $transferLimit['max_transferable']) {
            throw new \Exception("You can only transfer up to 30% of points gained in the last month. Maximum allowed: {$transferLimit['max_transferable']} points (30% of {$transferLimit['points_gained_last_month']} points gained).");
        }
    }

    /**
     * Validate user is not transferring to themselves
     *
     * @throws \Exception
     */
    protected function validateNotSelfTransfer(int $senderId, int $receiverId): void
    {
        if ($senderId === $receiverId) {
            throw new \Exception('Cannot transfer points to yourself.');
        }
    }

    /**
     * Create transfer record
     */
    protected function createTransferRecord(User $sender, User $receiver, array $data, string $familyCode): PointTransfer
    {
        return $this->pointTransferRepository->store([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'points' => $data['points'],
            'family_code' => $familyCode,
            'reason' => $data['reason'] ?? 'Points Transfer',
            'created_by' => auth()->id() ?? 1,
        ]);
    }

    /**
     * Update points for sender and receiver
     */
    protected function updateUserPoints(User $sender, User $receiver, int $points, int $totalDeduction): void
    {
        $sender->update(['points' => $sender->points - $totalDeduction]);
        $receiver->update(['points' => $receiver->points + $points]);
    }

    /**
     * Record point histories for transfer and fee
     */
    protected function recordPointHistories(User $sender, User $receiver, PointTransfer $transfer, int $points, int $fee): void
    {
        // Sender transfer deduction
        $this->createPointHistory([
            'user_id' => $sender->id,
            'amount' => $points,
            'points' => $sender->points,
            'score' => $sender->score,
            'subject_id' => $transfer->id,
            'subject_type' => PointTransfer::class,
        ]);

        // Sender fee deduction
        if ($fee > 0) {
            $this->createPointHistory([
                'user_id' => $sender->id,
                'amount' => $fee,
                'points' => $sender->points,
                'score' => $sender->score,
                'subject_id' => $transfer->id,
                'subject_type' => PointTransfer::class,
            ]);
        }

        // Receiver addition
        $this->createPointHistory([
            'user_id' => $receiver->id,
            'amount' => $points,
            'points' => $receiver->points,
            'score' => $receiver->score,
            'subject_id' => $transfer->id,
            'subject_type' => PointTransfer::class,
        ]);
    }

    /**
     * Create point history record
     */
    protected function createPointHistory(array $data): PointHistory
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
        [$startDate, $endDate] = $this->getLastMonthDateRange();

        $quizPoints = $this->calculateQuizPoints($userId, $startDate, $endDate);
        $bonusPoints = $this->calculateBonusPoints($userId, $startDate, $endDate);
        dd($bonusPoints);
        $transfersReceived = $this->calculateTransfersReceived($userId, $startDate, $endDate);

        return (int) ($quizPoints + $bonusPoints + $transfersReceived);
    }

    /**
     * Get last month date range
     */
    protected function getLastMonthDateRange(): array
    {
        return [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth(),
        ];
    }

    /**
     * Calculate quiz points for user
     */
    protected function calculateQuizPoints(int $userId, $startDate, $endDate): int
    {
        return PointHistory::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('subject_type', 'App\\Models\\Quiz')
            ->where('amount', '>', 0)
            ->sum('amount');
    }

    /**
     * Calculate bonus points (excluding penalties)
     */
    protected function calculateBonusPoints(int $userId, $startDate, $endDate): int
    {
        return PointHistory::where('point_histories.user_id', $userId)
            ->whereBetween('point_histories.created_at', [$startDate, $endDate])
            ->where('point_histories.subject_type', 'App\\Models\\BonusPenalty')
            ->join('bonuses_penalties', 'point_histories.subject_id', '=', 'bonuses_penalties.id')
            ->whereIn('bonuses_penalties.type', [BonusPenaltyType::BONUS, BonusPenaltyType::WELCOME_BONUS])
            ->sum('point_histories.amount');
    }

    /**
     * Calculate transfers received by user
     */
    protected function calculateTransfersReceived(int $userId, $startDate, $endDate): int
    {
        return PointHistory::where('point_histories.user_id', $userId)
            ->where('point_histories.subject_type', PointTransfer::class)
            ->whereBetween('point_histories.created_at', [$startDate, $endDate])
            ->join('point_transfers', function ($join) use ($userId) {
                $join->on('point_histories.subject_id', '=', 'point_transfers.id')
                    ->where('point_transfers.receiver_id', '=', $userId);
            })
            ->sum('point_histories.amount');
    }

    /**
     * Calculate maximum transferable points (30% of last month's gains)
     */
    public function getMaxTransferablePoints(int $userId): array
    {
        $pointsGainedLastMonth = $this->getPointsGainedLastMonth($userId);
        $maxTransferable = (int) floor($pointsGainedLastMonth * self::MAX_TRANSFER_PERCENTAGE);

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
        try {
            $sender = $this->userRepository->findById($senderId);
            $receiver = $this->userRepository->findById($receiverId);
            $errors = [];

            $errors = array_merge($errors, $this->validateFamilyCodesForValidation($sender, $receiver));
            $errors = array_merge($errors, $this->validatePointsForValidation($sender, $points, $senderId));
            $errors = array_merge($errors, $this->validateSelfTransferForValidation($senderId, $receiverId));

            $transferLimit = $this->getMaxTransferablePoints($senderId);

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

    /**
     * Validate family codes for validation endpoint
     */
    protected function validateFamilyCodesForValidation(User $sender, User $receiver): array
    {
        $errors = [];
        $senderFamilyCode = PointTransfer::extractFamilyCode($sender->membership_code);
        $receiverFamilyCode = PointTransfer::extractFamilyCode($receiver->membership_code);

        if (! $senderFamilyCode || ! $receiverFamilyCode) {
            $errors[] = 'Invalid membership code format.';
        } elseif ($senderFamilyCode != $receiverFamilyCode) {
            $errors[] = 'Users must be from the same family.';
        }

        return $errors;
    }

    /**
     * Validate points for validation endpoint
     */
    protected function validatePointsForValidation(User $sender, int $points, int $senderId): array
    {
        $errors = [];

        $transferFee = $this->calculateTransferFee($points);
        $totalRequired = $points + $transferFee;

        if ($sender->points < $totalRequired) {
            $errors[] = "Sender does not have enough points. Transfer requires {$totalRequired} points ({$points} transfer + {$transferFee} fee).";
        }

        $transferLimit = $this->getMaxTransferablePoints($senderId);
        if ($points > $transferLimit['max_transferable']) {
            $errors[] = "You can only transfer up to 30% of points gained in the last month. Maximum allowed: {$transferLimit['max_transferable']} points (30% of {$transferLimit['points_gained_last_month']} points gained).";
        }

        return $errors;
    }

    /**
     * Validate self transfer for validation endpoint
     */
    protected function validateSelfTransferForValidation(int $senderId, int $receiverId): array
    {
        if ($senderId === $receiverId) {
            return ['Cannot transfer points to yourself.'];
        }

        return [];
    }
}
