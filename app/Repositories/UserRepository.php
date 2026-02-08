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
use Illuminate\Support\Facades\DB;
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
            ],
            [
                'name' => $input->name,
                'email' => $input->email,
                'phone' => $input->phone,
                'password' => Hash::make($input->password),
            ]
        );

        if (! $user->roles->isNotEmpty()) {
            $user->assignRole('user');

        }

        $this->assignGroups($input->groups, $user);

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
        $user->load('groups');

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
        $groupIds = Group::whereIn('abbreviation', $group_names)->pluck('id');

        $groupsId = [1];
        if ($groupIds->isNotEmpty()) {
            $groupsId = array_merge($groupsId, $groupIds->toArray());
        }

        $user->groups()->sync($groupsId);
    }

    public function bonusAndPenalty($bonus)
    {
        $user = $this->findById($bonus->user_id);
        $points = ($bonus->type == BonusPenaltyType::BONUS || $bonus->type == BonusPenaltyType::WELCOME_BONUS) ? $bonus->points : -$bonus->points;
        $user->update([
            'points' => $user->points + $points,
        ]);

    }

    public function leaderboard($groupId = null)
    {
        $userGroupIds = auth()->user()->groups->pluck('id')->toArray();
        $isApi = request()->is('api/*');
        $authUserId = auth()->id();

        // Build the WHERE clause based on mode and group
        $whereClause = '';
        $bindings = [];

        if ($isApi) {
            if (isset($groupId)) {
                // API + specific group
                $whereClause = 'WHERE ug.group_id = ?';
                $bindings[] = $groupId;
            } else {
                // API + user groups except 1
                $placeholders = implode(',', array_fill(0, count($userGroupIds), '?'));
                $whereClause = "WHERE ug.group_id != 1 AND ug.group_id IN ($placeholders)";
                $bindings = $userGroupIds;
            }
        } else {
            if (isset($groupId)) {
                // Web + specific group
                $whereClause = 'WHERE ug.group_id = ?';
                $bindings[] = $groupId;
            } else {
                // Web + no group → group 1 only
                $whereClause = 'WHERE ug.group_id = 1';
            }
        }

        if ($isApi) {
            // For API mode, get top 10 users + auth user if not in top 10
            $usersTable = 'users';
            $groupsTable = 'user_groups';

            // Get top 10 users with their rank
            $top10Query = "
            SELECT DISTINCT
                u.id,
                u.name,
                u.email,
                u.score,
                u.points,
                u.membership_code,
                u.phone
            FROM {$usersTable} u
            INNER JOIN {$groupsTable} ug ON u.id = ug.user_id
            {$whereClause}
            ORDER BY u.score DESC
            LIMIT 10
        ";

            $top10 = DB::select($top10Query, $bindings);
            $top10Ids = collect($top10)->pluck('id')->toArray();

            // Check if auth user is in top 10
            if (! in_array($authUserId, $top10Ids)) {
                // Get auth user data
                $authUserQuery = "
                SELECT DISTINCT
                    u.id,
                    u.name,
                    u.email,
                    u.score,
                    u.points,
                    u.membership_code,
                    u.phone
                FROM {$usersTable} u
                INNER JOIN {$groupsTable} ug ON u.id = ug.user_id
                {$whereClause}
                AND u.id = ?
            ";

                $authBindings = array_merge($bindings, [$authUserId]);
                $authUser = DB::select($authUserQuery, $authBindings);

                if (! empty($authUser)) {
                    $results = array_merge($top10, $authUser);
                } else {
                    $results = $top10;
                }
            } else {
                $results = $top10;
            }

            // Calculate ranks for all users in the group
            $allUsersQuery = "
            SELECT DISTINCT
                u.id,
                u.score
            FROM {$usersTable} u
            INNER JOIN {$groupsTable} ug ON u.id = ug.user_id
            {$whereClause}
            ORDER BY u.score DESC
        ";

            $allUsers = DB::select($allUsersQuery, $bindings);
            $ranks = [];
            foreach ($allUsers as $index => $user) {
                $ranks[$user->id] = $index + 1;
            }

            // Convert to actual User models with relationships
            $userIds = collect($results)->pluck('id')->toArray();

            // Load actual User models with media relationship
            $users = $this->model->whereIn('id', $userIds)
                ->with('media')
                ->get()
                ->keyBy('id');

            // Return users in the same order as the results with ranks
            $orderedUsers = collect($userIds)->map(function ($id) use ($users, $ranks) {
                $user = $users->get($id);
                if ($user) {
                    $user->rank = $ranks[$id] ?? null;
                }

                return $user;
            })->filter();

            return $orderedUsers;
        }

        // Web mode: use your existing execute method
        $query = $this->model->query()
            ->with('media')
            ->select('id', 'name', 'email', 'membership_code', 'score', 'points')
            ->when(isset($groupId), function ($q) use ($groupId) {
                $q->whereHas('groups', function ($g) use ($groupId) {
                    $g->where('group_id', $groupId);
                });
            })
            ->when(! isset($groupId), function ($q) {
                $q->whereHas('groups', function ($g) {
                    $g->where('group_id', '=', 1);
                });
            })
            ->orderBy('score', 'desc');

        $users = $this->execute($query);

        // Add rank to each user
        $users->each(function ($user, $index) {
            $user->rank = $index + 1;
        });

        return $users;

    }

    public function getAdmins(?string $search = null)
    {
        $query = $this->model->query()->role('admin')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('membership_code', 'like', "%{$search}%");
                });
            })
            ->with(['media', 'groups'])
            ->latest();

        return $this->execute($query);
    }

    public function getTotalCount()
    {
        return $this->model->count();
    }

    public function getTotalFamilies()
    {
        $this->pagination = false;
        $users = $this->index([]);

        $familyCodes = $users->map(function ($user) {
            if (preg_match('/^(E\d+C\d+F\d+)/', $user->membership_code, $matches)) {
                return $matches[1];
            }

            return null;
        })
            ->filter()
            ->unique()
            ->values();

        return count($familyCodes);
    }

    public function searchFamilies(string $search)
    {
        return $this->model->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('membership_code', 'like', "%{$search}%");
        })
            ->get(['membership_code']);
    }

    public function getFamilyMembers(array $familyCodes)
    {
        return $this->model->where(function ($query) use ($familyCodes) {
            foreach ($familyCodes as $code) {
                $query->orWhere('membership_code', 'like', $code.'%');
            }
        })
            ->orderByRaw("SUBSTRING_INDEX(membership_code, 'F', 1), CAST(SUBSTRING_INDEX(membership_code, 'NR', -1) AS UNSIGNED)")
            ->get(['id', 'name', 'membership_code']);
    }

    public function getFamilyMembersWithGroups(string $familyCode)
    {
        return $this->model->where('membership_code', 'like', $familyCode.'%')
            ->with('groups:id,name')
            ->orderByRaw("CAST(SUBSTRING_INDEX(membership_code, 'NR', -1) AS UNSIGNED)")
            ->get(['id', 'name', 'membership_code', 'points', 'score']);
    }
}
