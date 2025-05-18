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

    public function store($name, $users)
    {
        $group = $this->model->create([
            'name' => $name,
        ]);

        if (! empty($users)) {
            $group->users()->sync($users);
        }

        return $group;
    }

    public function update($id, $name)
    {
        $group = $this->findById($id);
        $group->update([
            'name' => $name,
        ]);

        return $group;
    }

    public function updateUsers($id, $users)
    {
        $group = $this->findById($id);
        $group->users()->sync($users);

        return $group;
    }

    public function edit($id)
    {
        $group = $this->findById($id);
        $users = $group->users;

        return $users;
    }

    public function show($id)
    {
        $group = $this->findById($id);
        $group->load('users');

        return $group;
    }
}
