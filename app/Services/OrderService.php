<?php

namespace App\Services;

use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Events\OrderReceived;
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

    public function index($user_id = null, $status = null, $membership_code = null)
    {
        $orders = $this->orderRepository->index($user_id, $status, $membership_code);

        return $orders;
    }

    public function store($reward_id, $quantity)
    {
        $reward = $this->rewardRepository->findById($reward_id);
        $order = $this->orderRepository->store($reward, $quantity);
        $this->rewardRepository->redeemPoints($reward, $quantity);
        $this->userRepository->redeemPoints($order->points);

        PointHistory::addRecord($order);

        event(new OrderCreated($order));

        return $order;
    }

    public function received($id)
    {
        $order = $this->orderRepository->received($id);
        $order->load('servant');

        event(new OrderReceived($order));

        return $order;
    }

    public function myOrders()
    {
        $orders = $this->orderRepository->myOrders();
        $count = method_exists($orders, 'total') ? $orders->total() : $orders->count();
        $total_points = $this->orderRepository->totalPoints();

        return compact('orders', 'count', 'total_points');
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->cancel($id);
            $order->load('servant');
            $return = Returns::addRecord($order);
            $reward = $this->rewardRepository->findById($order->reward_id);
            $this->rewardRepository->returnRewards($reward, $order->quantity);
            $this->userRepository->returnReward($order->points, $order->user_id);
            RewardHistory::addRecord($return, $return->quantity);
            PointHistory::addRecord($return);

            DB::commit();

            event(new OrderCancelled($order));

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
