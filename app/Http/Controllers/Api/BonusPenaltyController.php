<?php

namespace App\Http\Controllers\Api;

use App\Services\BonusPenaltyService;
use Illuminate\Http\Request;

class BonusPenaltyController extends BaseController
{
    public function __construct(protected BonusPenaltyService $bonusPenaltyService) {}

    public function index(Request $request)
    {
        $bonusPenalties = $this->bonusPenaltyService->index($request->user_id);

        return $this->respondResource(\App\Http\Resources\BonusPenaltyResource::collection($bonusPenalties));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:1,2',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $bonusPenalty = $this->bonusPenaltyService->store($request->all());

        return $this->apiResponse(new \App\Http\Resources\BonusPenaltyResource($bonusPenalty));
    }

    public function show($id)
    {
        $bonusPenalty = $this->bonusPenaltyService->show($id);

        return $this->apiResponse(new \App\Http\Resources\BonusPenaltyResource($bonusPenalty));
    }
}
