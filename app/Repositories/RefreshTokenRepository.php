<?php

namespace App\Repositories;

use App\Models\RefreshToken;

class RefreshTokenRepository extends BaseRepository
{
    protected function model(): string
    {
        return RefreshToken::class;
    }

    public function __construct(RefreshToken $model)
    {
        parent::__construct($model);
    }

    public function store(array $data)
    {
        // allow device metadata to be stored if provided
        return $this->model->create($data);
    }

    public function findByToken(string $hashed)
    {
        return $this->model->where('token', $hashed)->first();
    }

    public function deleteByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }

    public function deleteByUserAndDevice($userId, $deviceType = null, $imei = null)
    {
        $query = $this->model->where('user_id', $userId);
        if (! is_null($deviceType)) {
            $query->where('device_type', $deviceType);
        }
        if (! is_null($imei)) {
            $query->where('imei', $imei);
        }

        return $query->delete();
    }
}
