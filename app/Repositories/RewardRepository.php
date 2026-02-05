<?php

namespace App\Repositories;

use App\DTOs\RewardCreateDTO;
use App\Enums\RewardStatus;
use App\Models\Reward;
use App\Models\RewardHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RewardRepository extends BaseRepository
{
    public function __construct(Reward $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return Reward::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function index()
    {
        $query = $this->model->query()->with(['media', 'group']);

        if (request()->is('api/*')) {
            $user = auth()->user();
            // Eager load groups to prevent N+1
            $user->loadMissing('groups');
            $groupIds = $user->groups->pluck('id')->toArray();
            if ($user && $user->groups->isNotEmpty()) {
                $query->whereIn('group_id', $groupIds);
            }
            $query->where('status', RewardStatus::ACTIVE);
        }
        $query->orderBy('status');

        return $this->execute($query);
    }

    public function store(RewardCreateDTO $input, $image)
    {
        $reward = $this->model->create([
            'name' => $input->name,
            'quantity' => $input->quantity,
            'status' => $input->status ?? RewardStatus::ACTIVE,
            'points' => $input->points,
            'group_id' => $input->group_id,
        ]);
        RewardHistory::addRecord($reward);
        if ($image) {
            $reward->addMedia($image)->toMediaCollection('rewards_images');
        }

        return $reward;
    }

    public function addQuantity($quantity, $id)
    {
        $reward = $this->findById($id);
        $reward->update([
            'quantity' => $reward->quantity + $quantity,
            'status' => RewardStatus::ACTIVE,
        ]);

        RewardHistory::addRecord($reward, $quantity);

        return $reward;
    }

    public function cancel($id)
    {
        $reward = $this->findById($id);
        $reward->fill([
            'status' => RewardStatus::CANCELLED,
            'quantity' => 0,
        ]);

        RewardHistory::addRecord($reward, $reward->getOriginal('quantity'));

        $reward->save();

        return $reward;
    }

    public function activate($id)
    {
        $reward = $this->findById($id);
        $reward->update([
            'status' => RewardStatus::ACTIVE,
        ]);

        RewardHistory::addRecord($reward);

        return $reward;
    }

    public function redeemPoints($reward, $quantity)
    {
        $status = $reward->quantity - $quantity <= 0
        ? RewardStatus::FINISHED
        : $reward->status;

        $reward->update([
            'quantity' => $reward->quantity - $quantity,
            'status' => $status,
        ]);
    }

    public function returnRewards($reward, $quantity)
    {
        $reward->update([
            'quantity' => $reward->quantity + $quantity,
            'status' => RewardStatus::ACTIVE,
        ]);
    }

    public function countActiveRewards(): int
    {
        return $this->model->where('status', RewardStatus::ACTIVE)->count();
    }

    public function calculateTotalPointsValue(): int
    {
        return $this->model->where('status', RewardStatus::ACTIVE)
            ->sum(DB::raw('points * quantity'));
    }

    public function calculateTotalQuantity(): int
    {
        return $this->model->where('status', RewardStatus::ACTIVE)
            ->sum('quantity');
    }
}
