<?php

namespace App\Services;

use App\Enums\BonusPenaltyStatus;
use App\Enums\BonusPenaltyType;
use App\Events\BonusPenaltyCreated;
use App\Models\PointHistory;
use App\Repositories\BonusPenaltyRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BonusPenaltyService
{
    public function __construct(
        protected BonusPenaltyRepository $bonusPenaltyRepository,
        protected UserRepository $userRepository,
    ) {}

    /**
     * Get list of bonus/penalties with optional user filter
     */
    public function index($user_id = null)
    {
        return $this->bonusPenaltyRepository->index($user_id);
    }

    /**
     * Get applied bonus/penalties with filters
     */
    public function getApplied(array $filters = [])
    {
        return $this->bonusPenaltyRepository->getApplied($filters);
    }

    /**
     * Get pending bonus/penalties for approval
     */
    public function getPending(array $filters = [])
    {
        return $this->bonusPenaltyRepository->getPending($filters);
    }

    /**
     * Store a new bonus/penalty
     */
    public function store(array $data)
    {
        try {
            DB::beginTransaction();

            // Check if creator is admin (cached)
            $isAdmin = $this->isAdmin();

            $data['created_by'] = auth()->id() ?? 1;
            $data['status'] = $isAdmin ? BonusPenaltyStatus::APPLIED : BonusPenaltyStatus::PENDING_APPROVAL;
            $data['approved_by'] = $isAdmin ? auth()->id() : null;

            $bonusPenalty = $this->bonusPenaltyRepository->store($data);

            // Only process points if status is applied (auto-approved by admin)
            if ($isAdmin) {
                $this->processPoints($bonusPenalty);
            }

            DB::commit();

            return $bonusPenalty;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get single bonus/penalty by ID
     */
    public function show($id)
    {
        return $this->bonusPenaltyRepository->findByIdWithRelations($id);
    }

    /**
     * Approve a bonus/penalty
     */
    public function approve($bonusPenalty)
    {
        try {
            DB::beginTransaction();

            $bonusPenalty->status = BonusPenaltyStatus::APPLIED;
            $bonusPenalty->approved_by = auth()->id();
            $bonusPenalty->save();

            // Process points
            $this->processPoints($bonusPenalty);

            DB::commit();

            return $bonusPenalty;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reject a bonus/penalty
     */
    public function reject($bonusPenalty)
    {
        $bonusPenalty->status = BonusPenaltyStatus::REJECTED;
        $bonusPenalty->approved_by = auth()->id();
        $bonusPenalty->save();

        return $bonusPenalty;
    }

    /**
     * Create welcome bonus for new user
     */
    public function welcomeBonus($user)
    {
        try {
            DB::beginTransaction();

            $bonusPenalty = $this->bonusPenaltyRepository->store([
                'user_id' => $user->id,
                'points' => 50,
                'type' => BonusPenaltyType::WELCOME_BONUS,
                'reason' => _('messages.Welcome points'),
                'status' => BonusPenaltyStatus::APPLIED,
                'created_by' => 1,
            ]);

            $this->processPoints($bonusPenalty);

            DB::commit();

            return $bonusPenalty;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process points update and notification (extracted for reusability)
     */
    protected function processPoints($bonusPenalty)
    {
        // Add to point history
        PointHistory::addRecord($bonusPenalty);

        // Update user points
        $this->userRepository->bonusAndPenalty($bonusPenalty);

        // Fire event to send notification (async)
        event(new BonusPenaltyCreated($bonusPenalty));
    }

    /**
     * Check if current user is admin (cached for performance)
     */
    protected function isAdmin(): bool
    {
        return Cache::remember(
            'user_is_admin_'.auth()->id(),
            now()->addMinutes(10),
            fn () => auth()->user()?->hasRole('admin') ?? false
        );
    }
}
