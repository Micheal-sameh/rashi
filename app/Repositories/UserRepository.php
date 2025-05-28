<?php

namespace App\Repositories;

use App\DTOs\UserLoginDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

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

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
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

    public function index($input)
    {
        $query = $this->model->query()
            ->when(! is_null($input), function ($query) use ($input) {
                $query->when($input->has('name'), fn ($q) => $q->where('name', 'like', '%'.$input->name.'%')
                )->when($input->has('group_id'), function ($query) use ($input) {
                    $query->whereHas('groups', fn ($q) => $q->where('group_id', $input->group_id));
                });
            })->orderBy($input->sort_by ?? 'name', $input->direction ?? 'asc');

        return $this->execute($query);
    }

    public function show($id)
    {
        return $this->findById($id);
    }

    public function profilePic($image)
    {
        $user = $this->findById(Auth::id());
        $user->addMedia($image)->toMediaCollection('profile_images');

        return $user;
    }

    public function updatePoints($data)
    {
        $user = auth()->user();
        $user->update([
            'points' => $user->points + $data['score'],
            'score' => $user->score + $data['score'],
        ]);

        return $user;
    }

    public function dropdown()
    {
        return $this->model->all();
    }

    public function updateGroups($groups, $id)
    {
        $user = $this->findById($id);
        $user->groups()->sync($groups);

        return $user;
    }

    public function redeemPoints($points)
    {
        return auth()->user()->update([
            'points' => auth()->user()->points - $points,
        ]);
    }

    public function returnReward($points)
    {
        return auth()->user()->update([
            'points' => auth()->user()->points + $points,
        ]);
    }
}
