<?php

namespace App\Repositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupRepository extends BaseRepository
{
    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return Group::class;
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

    public function store($name)
    {
        return $this->model->create([
            'name' => $name,
        ]);
    }

    public function update($id, $name)
    {
        $group = $this->findById($id);
        $group->update([
            'name' => $name,
        ]);

        return $group;
    }
}
