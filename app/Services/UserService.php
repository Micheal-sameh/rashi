<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function updateOrcreate($input)
    {
        $user = $this->userRepository->updateOrcreate($input);
        $user->load('roles');

        return $user;
    }
}
