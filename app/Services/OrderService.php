<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\RewardRepository;
use App\Repositories\UserRepository;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected RewardRepository $rewardRepository,
        protected UserRepository $userRepository,
    ) {}

    public function index()
    {
        $orders = $this->orderRepository->index();
        $orders->load('servant', 'reward', 'user');

        return $orders;
    }

    public function store($reward_id, $quantity)
    {
        $reward = $this->rewardRepository->findById($reward_id);
        $order = $this->orderRepository->store($reward, $quantity);
        $this->rewardRepository->redeemPoints($reward, $quantity);
        $this->userRepository->redeemPoints($order->points);

        return $order;
    }

    public function received($id)
    {
        $order = $this->orderRepository->received($id);
        $order->load('servant');

        return $order;
    }
}
