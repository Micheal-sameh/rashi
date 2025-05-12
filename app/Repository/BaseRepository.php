<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->getModelInstance();
    }

    // Child classes must define this method
    abstract protected function model(): string;

    protected function getModelInstance(): Model
    {
        return app($this->model());
    }

    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }
}
