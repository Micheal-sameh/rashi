<?php

namespace App\Services;

use App\Models\PointHistory;
use App\Repositories\OrderRepository;
use App\Repositories\PointHistoryRepository;
use App\Repositories\RewardRepository;
use App\Repositories\UserRepository;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected RewardRepository $rewardRepository,
        protected UserRepository $userRepository,
        protected PointHistoryRepository $pointHistoryRepository,
    ) {}

    public function index($user_id = null, $status = null)
    {
        $orders = $this->orderRepository->index($user_id, $status);
        $orders->load('servant', 'reward', 'user');

        return $orders;
    }

    public function store($reward_id, $quantity)
    {
        $reward = $this->rewardRepository->findById($reward_id);
        $order = $this->orderRepository->store($reward, $quantity);
        $this->rewardRepository->redeemPoints($reward, $quantity);
        $this->userRepository->redeemPoints($order->points);

        PointHistory::addRecord($order);

        return $order;
    }

    public function received($id)
    {
        $order = $this->orderRepository->received($id);
        $order->load('servant');

        return $order;
    }
}
