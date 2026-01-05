<?php

namespace App\Http\Controllers;

use App\DTOs\RewardCreateDTO;
use App\Http\Requests\AddQuantityRequest;
use App\Http\Requests\RewardCreateRequest;
use App\Repositories\GroupRepository;
use App\Services\RewardService;

class RewardController extends Controller
{
    public function __construct(
        protected RewardService $rewardService,
        protected GroupRepository $groupRepository,
    ) {}

    public function index()
    {
        $data = $this->rewardService->index();
        $rewards = $data['rewards'];
        $activeRewardsCount = $data['activeRewardsCount'];
        $totalPointsValue = $data['totalPointsValue'];
        $totalQuantity = $data['totalQuantity'];

        return view('rewards.index', compact('rewards', 'activeRewardsCount', 'totalPointsValue', 'totalQuantity'));
    }

    public function create()
    {
        $groups = $this->groupRepository->dropdown();

        return view('rewards.create', compact('groups'));
    }

    public function store(RewardCreateRequest $request)
    {
        $input = new RewardCreateDTO(...$request->only(
            'name', 'quantity', 'points', 'status', 'group_id'
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

    public function cancel($id)
    {
        $reward = $this->rewardService->cancel($id);

        return response()->json([
            'success' => true,
            'reward' => $reward,
            'status_text' => \App\Enums\RewardStatus::getStringValue($reward->status),
        ]);
    }
}
