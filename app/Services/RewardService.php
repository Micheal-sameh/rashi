<?php

namespace App\Services;

use App\Repositories\RewardRepository;

class RewardService
{
    public function __construct(protected RewardRepository $rewardRepository) {}

    public function index()
    {
        $rewards = $this->rewardRepository->index();
        $activeRewardsCount = $this->rewardRepository->countActiveRewards();
        $totalPointsValue = $this->rewardRepository->calculateTotalPointsValue();
        $totalQuantity = $this->rewardRepository->calculateTotalQuantity();

        return compact('rewards', 'activeRewardsCount', 'totalPointsValue', 'totalQuantity');
    }

    public function store($input, $image)
    {
        return $this->rewardRepository->store($input, $image);
    }

    public function update($rewards, $files)
    {
        return $this->rewardRepository->update($rewards, $files);
    }

    public function addQuantity($quantity, $id)
    {
        return $this->rewardRepository->addQuantity($quantity, $id);
    }

    public function cancel($id)
    {
        return $this->rewardRepository->cancel($id);
    }
}
