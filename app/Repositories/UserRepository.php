<?php

namespace App\Repositories;

use App\DTOs\UserLoginDTO;
use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return User::class;
    }

    public function updateOrCreate(UserLoginDTO $input): User
    {
        if ($input->password === null) {
            $input->password = 'Ar-Rashi';
        }

        $user = $this->model->updateOrCreate(
            [
                'membership_code' => $input->membership_code,
                'name' => $input->name,
            ],
            [
                'email' => $input->email,
                'phone' => $input->phone,
                'password' => bcrypt($input->password),
            ]
        );

        if (! $user->hasRole('user')) {
            $user->assignRole('user');
        }

        return $user;
    }
}
