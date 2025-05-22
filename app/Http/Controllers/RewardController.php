<?php

namespace App\Http\Controllers;

use App\DTOs\RewardCreateDTO;
use App\Http\Requests\AddQuantityRequest;
use App\Http\Requests\RewardCreateRequest;
use App\Services\RewardService;

class RewardController extends Controller
{
    public function __construct(protected RewardService $rewardService) {}

    public function index()
    {
        $rewards = $this->rewardService->index();

        return view('rewards.index', compact('rewards'));
    }

    public function create()
    {
        return view('rewards.create');
    }

    public function store(RewardCreateRequest $request)
    {
        $input = new RewardCreateDTO(...$request->only(
            'name', 'quantity', 'points', 'status'
        ));
        $this->rewardService->store($input, $request->image);

        return redirect()->route('rewards.index')->with('success', 'Reward created successfully');
    }

    public function addQuantity(AddQuantityRequest $request, $id)
    {
        $reward = $this->rewardService->addQuantity($request->quantity, $id);

        return response()->json([
            'success' => true,
            'reward' => $reward,
        ]);
    }
}
