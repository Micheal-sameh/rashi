<?php

namespace App\Services;

use App\Models\PointHistory;
use App\Models\Returns;
use App\Models\RewardHistory;
use App\Repositories\OrderRepository;
use App\Repositories\PointHistoryRepository;
use App\Repositories\RewardRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

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

    public function myOrders()
    {
        $orders = $this->orderRepository->myOrders();
        $orders->load('servant', 'reward', 'user');
        $count = method_exists($orders, 'total') ? $orders->total() : $orders->count();
        $total_points = $this->orderRepository->totalPoints();

        return compact('orders', 'count', 'total_points');
    }

    public function cancel($id)
    {
        DB::beginTransaction();
        $order = $this->orderRepository->cancel($id);
        $order->load('servant');
        $return = Returns::addRecord($order);
        $reward = $this->rewardRepository->findById($order->reward_id);
        $this->rewardRepository->returnRewards($reward, $order->quantity);
        $this->userRepository->returnReward($order->points);
        RewardHistory::addRecord($return, $return->quantity);
        PointHistory::addRecord($return);
        DB::commit();

        return $order;
    }
}
