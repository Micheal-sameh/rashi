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

    public function index()
    {
        $user = Auth::user();
        $query = $this->model
            ->when(! $user->can('view_all_orders'), fn ($q) => $q->where('user_id', $user->id))
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
}
