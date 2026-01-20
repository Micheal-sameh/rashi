<?php

namespace App\Repositories;

use App\Models\FcmToken;

class FcmTokenRepository extends BaseRepository
{
    protected function model(): string
    {
        return FcmToken::class;
    }

    public function __construct(FcmToken $model)
    {
        parent::__construct($model);
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    public function updateOrCreate($data)
    {
        return $this->model->updateOrCreate(
            ['user_id' => $data['user_id'], 'imei' => $data['imei']],
            $data
        );
    }

    public function getTokensByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->pluck('token')->unique()->values()->toArray();
    }

    public function deleteByToken($token)
    {
        return $this->model->where('token', $token)->where('user_id', auth()->id())->delete();
    }

    public function deleteByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }
}
