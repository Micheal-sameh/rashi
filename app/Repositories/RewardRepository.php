<?php

namespace App\Repositories;

use App\DTOs\RewardCreateDTO;
use App\Enums\RewardStatus;
use App\Models\Reward;
use App\Models\RewardHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $query = $this->model->query();

        return $this->execute($query);
    }

    public function store(RewardCreateDTO $input, $image)
    {
        $reward = $this->model->create([
            'name' => $input->name,
            'quantity' => $input->quantity,
            'status' => $input->status ?? RewardStatus::ACTIVE,
            'points' => $input->points,
        ]);
        RewardHistory::addRecord($reward);
        if ($image) {
            $reward->addMedia($image)->toMediaCollection('rewards_images');
        }
    }

    public function addQuantity($quantity, $id)
    {
        $reward = $this->findById($id);
        $reward->update([
            'quantity' => $reward->quantity + $quantity,
        ]);
        RewardHistory::addRecord($reward);

        return $reward;
    }
}
