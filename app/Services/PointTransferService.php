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

            // Validate: Sender has enough points
            if ($sender->points < $data['points']) {
                throw new \Exception('Sender does not have enough points.');
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

            // Deduct points from sender
            $sender->update([
                'points' => $sender->points - $data['points'],
            ]);

            // Add points to receiver
            $receiver->update([
                'points' => $receiver->points + $data['points'],
            ]);

            // Create point history for sender (deduction)
            $this->createPointHistory([
                'user_id' => $sender->id,
                'amount' => $data['points'],
                'points' => $sender->points,
                'score' => $sender->score,
                'subject_id' => $transfer->id,
                'subject_type' => PointTransfer::class,
                'type' => 'deduction',
            ]);

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

            // Check not same user
            if ($sender->id === $receiver->id) {
                $errors[] = 'Cannot transfer points to yourself.';
            }

            return [
                'valid' => empty($errors),
                'errors' => $errors,
                'sender' => $sender,
                'receiver' => $receiver,
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }
}
