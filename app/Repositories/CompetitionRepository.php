<?php

namespace App\Repositories;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionRepository extends BaseRepository
{
    public function __construct(Competition $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return Competition::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function index()
    {
        $query = $this->model
            ->where('status', '!=', CompetitionStatus::CANCELLED);

        return $this->execute($query);
    }

    public function show($id)
    {
        return $this->findById($id);
    }

    public function store($input, $image)
    {
        $competition = $this->model->create([
            'name' => $input->name,
            'start_at' => Carbon::parse($input->start_at),
            'end_at' => Carbon::parse($input->end_at),
            'status' => $input->start_at > today() ? CompetitionStatus::PENDING : CompetitionStatus::ACTIVE,
        ]);
        $competition->addMedia($image)->toMediaCollection('competitions_images');

        return $competition;
    }

    public function update($id, $input, $image)
    {
        $competition = $this->findById($id);
        $competition->update([
            'name' => $input->name,
            'start_at' => Carbon::parse($input->start_at),
            'end_at' => Carbon::parse($input->end_at),
        ]);
        if ($image) {
            $competition->clearMediaCollection('competitions_images');
            $competition->addMedia($image)->toMediaCollection('competitions_images');
        }

        return $competition;
    }

    public function cancel($id)
    {
        $competition = $this->findById($id);
        $competition->update(['status' => CompetitionStatus::CANCELLED]);
    }
}
