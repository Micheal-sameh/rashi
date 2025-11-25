<?php

namespace App\Repositories;

use App\DTOs\UserLoginDTO;
use App\Enums\BonusPenaltyType;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

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

    public int $perPage = 20;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function updateOrCreate(UserLoginDTO $input): User
    {
        if ($input->password === null) {
            $input->password = 'password';
        }

        $user = $this->model->updateOrCreate(
            [
                'membership_code' => $input->membership_code,
                'name' => $input->name,
            ],
            [
                'email' => $input->email,
                'phone' => $input->phone,
                'password' => Hash::make($input->password),
            ]
        );

        if (! $user->roles->isNotEmpty()) {
            $user->assignRole('user');

        }

        $this->assignGroups($input->group, $user);

        return $user;
    }

    public function index($input)
    {
        $query = $this->model->query()->with([
            'roles:id,name',
            'media:id,model_id,model_type,file_name,collection_name,disk',
        ])->when($input, function ($query) use ($input) {
            $query
                ->when(isset($input->name), fn ($q) => $q->where('name', 'like', '%'.$input->name.'%')
                )
                ->when(isset($input->group_id), fn ($q) => $q->whereHas('groups', fn ($g) => $g->where('group_id', $input->group_id)
                )
                );
        })
            ->orderBy($input->sort_by ?? 'name', $input->direction ?? 'asc');

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
        $user = Cache::get('auth_user_'.auth()->id()) ?? auth()->user();
        $user->update([
            'points' => $user->points + $data['score'],
            'score' => $user->score + $data['score'],
        ]);

        // Clear cache after update
        Cache::forget('auth_user_'.$user->id);

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
        $user = Cache::get('auth_user_'.auth()->id()) ?? auth()->user();
        $result = $user->update([
            'points' => $user->points - $points,
        ]);

        // Clear cache after update
        Cache::forget('auth_user_'.$user->id);

        return $result;
    }

    public function returnReward($points, $user_id)
    {
        $user = $this->findById($user_id);
        $result = $user->update([
            'points' => $user->points + $points,
        ]);

        return $result;
    }

    protected function assignGroups($group_name, $user)
    {
        $groupId = Group::where('abbreviation', $group_name)->value('id');

        $groupsId = [1];
        if ($groupId) {
            $groupsId[] = $groupId;
        }
        $user->groups()->sync($groupsId);
    }

    public function bonusAndPenalty($bonus)
    {
        $user = $this->findById($bonus->user_id);
        $points = $bonus->type == BonusPenaltyType::BONUS ? $bonus->points : -$bonus->points;
        $user->update([
            'points' => $user->points + $points,
        ]);

    }

    public function leaderboard($groupId)
    {
        return $this->model->query()->with('media')
            ->select('id', 'name', 'score', 'points')
            ->when(! isset($groupId), function ($query) {
                $query->whereHas('groups', function ($q) {
                    $q->where('group_id', '!=', 1);
                });
            })->when(isset($groupId), function ($query) use ($groupId) {
                $query->whereHas('groups', function ($q) use ($groupId) {
                    $q->where('group_id', $groupId);
                });
            })
            ->orderBy('score', 'desc')
            ->limit(10)
            ->get();
    }

    public function leaderboardByGroup($user)
    {
        $groupIds = $user->groups->pluck('id')->filter(function ($groupId) {
            $group = \App\Models\Group::find($groupId);

            return $group && $group->abbreviation !== 'general';
        });

        if ($groupIds->isEmpty()) {
            return collect(); // Return empty collection if no valid groups
        }

        return $this->model->query()->with('media')
            ->select('id', 'name', 'score', 'points')
            ->whereHas('groups', function ($query) use ($groupIds) {
                $query->whereIn('groups.id', $groupIds);
            })
            ->orderBy('score', 'desc')
            ->limit(10)
            ->get();
    }
}
