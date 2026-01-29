<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected BonusPenaltyService $bonusPenaltyService
    ) {}

    public function updateOrcreate($input)
    {
        DB::beginTransaction();
        $user = $this->userRepository->updateOrcreate($input);
        $user->load('roles', 'groups', 'media');

        // Award welcome bonus if user is newly created
        if ($user->wasRecentlyCreated) {
            $this->bonusPenaltyService->welcomeBonus($user);
        }
        DB::commit();

        return $user;
    }

    public function index($input = null)
    {
        $users = $this->userRepository->index($input);

        return $users;
    }

    public function show($id)
    {
        $user = $this->userRepository->show($id);
        $user->load('roles', 'groups', 'media');

        return $user;
    }

    public function profilePic($image)
    {
        $user = $this->userRepository->profilePic($image);
        $user->load('roles', 'groups', 'media');

        return $user;
    }

    public function updateGroups($groups, $id)
    {
        $user = $this->userRepository->updateGroups($groups, $id);
        $user->load('roles', 'groups', 'media');

        return $user;
    }

    public function leaderboard($groupId = null)
    {
        $users = $this->userRepository->leaderboard($groupId);

        return $users;
    }

    public function getAdmins(?string $search = null)
    {
        return $this->userRepository->getAdmins($search);
    }
}
