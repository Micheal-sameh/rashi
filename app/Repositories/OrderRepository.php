<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\RewardHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return Order::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function index($user_id, $status, $membership_code = null)
    {
        $user = Auth::user();
        $query = $this->model->query()
            ->withApiRelations()
            ->when(! $user->can('view_all_orders'), fn ($q) => $q->where('user_id', $user->id))
            ->when(isset($user_id), fn ($q) => $q->where('user_id', $user_id))
            ->when(isset($status), fn ($q) => $q->where('status', $status))
            ->when(isset($membership_code), fn ($q) => $q->whereHas('user', function ($query) use ($membership_code) {
                $query->where('membership_code', 'like', '%'.$membership_code.'%');
            }))
            ->orderBy('status')
            ->latest();

        return $this->execute($query);
    }

    public function store($reward, $quantity)
    {
        $order = $this->model->create([
            'reward_id' => $reward->id,
            'quantity' => $quantity,
            'points' => $reward->points * $quantity,
            'status' => OrderStatus::PENDING,
            'user_id' => Auth::id(),
        ]);
        RewardHistory::addRecord($order);

        return $order;
    }

    public function received($id)
    {
        $order = $this->findById($id);
        $order->update([
            'status' => OrderStatus::COMPLETED,
            'servant_id' => Auth::id(),
        ]);

        return $order;
    }

    public function myOrders()
    {
        $query = $this->model->query()
            ->withApiRelations()
            ->where('user_id', Auth::id())
            ->latest();

        return $this->execute($query);
    }

    // used with my orders
    public function totalPoints()
    {
        return $this->model->where('user_id', Auth::id())->sum('points');
    }

    public function cancel($id)
    {
        $order = $this->findById($id);
        $order->update([
            'status' => OrderStatus::CANCELLED,
        ]);

        return $order;
    }

    public function getPendingOrdersCount()
    {
        return $this->model->where('status', OrderStatus::PENDING)->count();
    }
}
