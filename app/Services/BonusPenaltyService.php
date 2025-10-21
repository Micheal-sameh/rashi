<?php

namespace App\Services;

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
        $bonusPenalties->load(['user', 'creator']);

        return $bonusPenalties;
    }

    public function store($data)
    {
        DB::beginTransaction();
        $data['created_by'] = auth()->id();
        $bonusPenalty = $this->bonusPenaltyRepository->store($data);
        PointHistory::addRecord($bonusPenalty);
        // Update user points
        $this->userRepository->bonusAndPenalty($bonusPenalty);
        DB::commit();

        return $bonusPenalty;
    }

    public function show($id)
    {
        return $this->bonusPenaltyRepository->findById($id);
    }
}
