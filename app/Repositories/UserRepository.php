<?php

namespace App\Repositories;

use App\Enums\BonusPenaltyType;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

    public function updateOrCreate($input): User
    {
        $user = $this->model->updateOrCreate(
            [
                'membership_code' => strtoupper($input['membership_code']),
            ],
            [
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => isset($input['phone']) ? $input['phone'] : null,
                'ar_token' => $input['ar_token'] ?? null,
            ]
        );

        if (! $user->roles->isNotEmpty()) {
            $user->assignRole('user');

        }

        if (isset($input['groups'])) {
            $this->assignGroups($input['groups'], $user);
        }

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
        return $this->model->orderBy('name')->get(['name', 'id']);
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

    protected function assignGroups($group_names, $user)
    {
        $groupIds = Group::whereIn('abbreviation', $group_names)->value('id');

        $groupsIds = [1];
        if ($groupIds) {
            $groupsId[] = $groupIds;
        }
        $user->groups()->sync($groupsIds);
    }

    public function bonusAndPenalty($bonus)
    {
        $user = $this->findById($bonus->user_id);
        $points = $bonus->type == BonusPenaltyType::BONUS ? $bonus->points : -$bonus->points;
        $user->update([
            'points' => $user->points + $points,
        ]);

    }

    public function leaderboard($groupId = null)
    {
        $userGroupIds = auth()->user()->groups->pluck('id')->toArray();
        $isApi = request()->is('api/*');

        return $this->model->query()
            ->with('media')
            ->select('id', 'name', 'score', 'points')

            // API mode
            ->when($isApi, function ($query) use ($groupId, $userGroupIds) {
                $query
                    // API + specific group
                    ->when(isset($groupId), function ($q) use ($groupId) {
                        $q->whereHas('groups', function ($g) use ($groupId) {
                            $g->where('group_id', $groupId);
                        });
                    })
                    // API + no group → user groups except 1
                    ->when(! isset($groupId), function ($q) use ($userGroupIds) {
                        $q->whereHas('groups', function ($g) use ($userGroupIds) {
                            $g->where('group_id', '!=', 1)
                                ->whereIn('group_id', $userGroupIds);
                        });
                    });
            })

            // Web mode
            ->when(! $isApi, function ($query) use ($groupId) {
                $query
                    // Web + specific group
                    ->when(isset($groupId), function ($q) use ($groupId) {
                        $q->whereHas('groups', function ($g) use ($groupId) {
                            $g->where('group_id', $groupId);
                        });
                    })
                    // Web + no group → exclude group 1
                    ->when(! isset($groupId), function ($q) {
                        $q->whereHas('groups', function ($g) {
                            $g->where('group_id', '=', 1);
                        });
                    });
            })

            ->orderBy('score', 'desc')
            ->limit(10)
            ->get();
    }
}
