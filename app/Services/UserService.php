<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function updateOrcreate($input)
    {
        $user = $this->userRepository->updateOrcreate($input);
        $user->load('roles', 'groups');

        return $user;
    }

    public function index($input = null)
    {
        $users = $this->userRepository->index($input);
        $users->load('roles');

        return $users;
    }

    public function show($id)
    {
        $user = $this->userRepository->show($id);
        $user->load('roles');

        return $user;
    }

    public function profilePic($image)
    {
        $user = $this->userRepository->profilePic($image);
        $user->load('roles');

        return $user;
    }

    public function updateGroups($groups, $id)
    {
        $user = $this->userRepository->updateGroups($groups, $id);
        $user->load('groups');

        return $user;
    }
}
