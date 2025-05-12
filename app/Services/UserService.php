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

    public function index()
    {
        $users = $this->userRepository->index();
        $users->load('roles');

        return $users;
    }

    public function show($id)
    {
        $user = $this->userRepository->show($id);
        $user->load('roles');

        return $user;
    }
}
