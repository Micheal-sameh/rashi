<?php

namespace App\Services;

use App\Repositories\RewardRepository;

class RewardService
{
    public function __construct(protected RewardRepository $rewardRepository) {}

    public function index()
    {
        $rewards = $this->rewardRepository->index();

        return $rewards;
    }

    public function store($input, $image)
    {
        return $this->rewardRepository->store($input, $image);
    }

    public function update($rewards, $files)
    {
        return $this->rewardRepository->update($rewards, $files);
    }
}
