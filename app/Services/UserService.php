<?php

namespace App\Services;

use App\Enums\BonusPenaltyType;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected BonusPenaltyService $bonusPenaltyService
    ) {}

    public function updateOrcreate($input)
    {
        $user = $this->userRepository->updateOrcreate($input);
        $user->load('roles', 'groups', 'media');

        // Award welcome bonus if user is newly created
        if ($user->wasRecentlyCreated) {
            $this->bonusPenaltyService->store([
                'user_id' => $user->id,
                'points' => 50,
                'type' => BonusPenaltyType::WELCOME_BONUS,
                'reason' => _('messages.Welcome points'),
            ]);
        }

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
}
