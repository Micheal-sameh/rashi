<?php

namespace App\Services;

use App\Models\Returns;
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
        protected HistoryService $historyService,
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

        if ($reward->quantity < $quantity) {
            throw new \Exception('Insufficient reward quantity');
        }

        DB::beginTransaction();
        try {
            $order = $this->orderRepository->store($reward, $quantity);
            $this->rewardRepository->redeemPoints($reward, $quantity);
            $this->userRepository->redeemPoints($order->points);

            $this->historyService->addPointHistory($order);
            $this->historyService->addRewardHistory($order);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
        $this->historyService->addRewardHistory($return, $return->quantity);
        $this->historyService->addPointHistory($return);
        DB::commit();

        return $order;
    }
}
