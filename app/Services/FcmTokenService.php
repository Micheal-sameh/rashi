<?php

namespace App\Services;

use App\Repositories\FcmTokenRepository;

class FcmTokenService
{
    public function __construct(protected FcmTokenRepository $fcmTokenRepository) {}

    public function updateOrCreate($data)
    {
        return $this->fcmTokenRepository->updateOrCreate($data);
    }

    public function getTokensByUserId($userId)
    {
        return $this->fcmTokenRepository->getTokensByUserId($userId);
    }

    public function deleteByToken($token)
    {
        return $this->fcmTokenRepository->deleteByToken($token);
    }

    public function deleteByUserId($userId)
    {
        return $this->fcmTokenRepository->deleteByUserId($userId);
    }
}
