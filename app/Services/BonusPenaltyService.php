<?php

namespace App\Services;

use App\Enums\BonusPenaltyStatus;
use App\Enums\BonusPenaltyType;
use App\Events\BonusPenaltyCreated;
use App\Models\PointHistory;
use App\Repositories\BonusPenaltyRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class BonusPenaltyService
{
    public function __construct(
        protected BonusPenaltyRepository $bonusPenaltyRepository,
        protected UserRepository $userRepository,
    ) {}

    public function index($user_id = null)
    {
        $bonusPenalties = $this->bonusPenaltyRepository->index($user_id);

        return $bonusPenalties;
    }

    public function store($data)
    {
        DB::beginTransaction();

        // Check if creator is admin
        $creator = \App\Models\User::find(auth()->id());
        $isAdmin = $creator && $creator->hasRole('admin');

        $data['created_by'] = auth()->id() ?? 1;
        $data['status'] = $isAdmin ? \App\Enums\BonusPenaltyStatus::APPLIED : \App\Enums\BonusPenaltyStatus::PENDING_APPROVAL;
        $data['approved_by'] = $isAdmin ? auth()->id() : null;

        $bonusPenalty = $this->bonusPenaltyRepository->store($data);

        // Only process points if status is applied (auto-approved by admin)
        if ($isAdmin) {
            PointHistory::addRecord($bonusPenalty);
            // Update user points
            $this->userRepository->bonusAndPenalty($bonusPenalty);
            // Fire event to send notification
            event(new BonusPenaltyCreated($bonusPenalty));
        }

        DB::commit();

        return $bonusPenalty;
    }

    public function show($id)
    {
        return $this->bonusPenaltyRepository->findById($id);
    }

    public function approve($bonusPenalty)
    {
        DB::beginTransaction();

        $bonusPenalty->status = BonusPenaltyStatus::APPLIED;
        $bonusPenalty->approved_by = auth()->id();
        $bonusPenalty->save();

        // Process points
        PointHistory::addRecord($bonusPenalty);
        // Update user points
        $this->userRepository->bonusAndPenalty($bonusPenalty);
        // Fire event to send notification
        event(new BonusPenaltyCreated($bonusPenalty));

        DB::commit();

        return $bonusPenalty;
    }

    public function reject($bonusPenalty)
    {
        $bonusPenalty->status = BonusPenaltyStatus::REJECTED;
        $bonusPenalty->approved_by = auth()->id();
        $bonusPenalty->save();

        return $bonusPenalty;
    }

    public function welcomeBonus($user)
    {
        $bonusPenalty = $this->store([
            'user_id' => $user->id,
            'points' => 50,
            'type' => BonusPenaltyType::WELCOME_BONUS,
            'reason' => _('messages.Welcome points'),
            'status' => BonusPenaltyStatus::APPLIED,
        ]);

        PointHistory::addRecord($bonusPenalty);
        // Update user points
        $this->userRepository->bonusAndPenalty($bonusPenalty);
        // Fire event to send notification
        event(new BonusPenaltyCreated($bonusPenalty));
    }
}
