<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RewardResource;
use App\Services\RewardService;

class RewardController extends BaseController
{
    public function __construct(protected RewardService $rewardService) {}

    public function index()
    {
        $rewards = $this->rewardService->index();

        return $this->respondResource(RewardResource::collection($rewards));
    }
}
