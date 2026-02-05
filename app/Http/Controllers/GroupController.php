<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionStatus;
use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupUsers;
use App\Repositories\CompetitionRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;

class GroupController extends Controller
{
    public function __construct(
        protected GroupRepository $groupRepository,
        protected UserRepository $userRepository,
        protected CompetitionRepository $competitionRepository,
    ) {}

    public function index()
    {
        $groups = $this->groupRepository->index();
        $totalGroups = $this->groupRepository->getTotalCount();

        return view('groups.index', compact('groups', 'totalGroups'));
    }

    public function create()
    {
        $users = $this->userRepository->dropdown();

        return view('groups.create', compact('users'));
    }

    public function store(CreateGroupRequest $request)
    {
        $this->groupRepository->store($request->name, $request->abbreviation, $request->users);

        return redirect()->route('groups.index')->with('success', 'Group created successfully');
    }

    public function update($id, CreateGroupRequest $request)
    {
        $group = $this->groupRepository->update($id, $request->name, $request->abbreviation);

        return redirect()->route('groups.index')->with('success', "Group $group->name updated successfully");
    }

    public function updateUsers($id, UpdateGroupUsers $request)
    {
        $group = $this->groupRepository->updateUsers($id, $request->users);

        return redirect()->route('groups.index')->with('success', "Group $group->name updated successfully");
    }

    public function usersedit($id)
    {
        $group = $this->groupRepository->show($id);
        $users = $this->userRepository->dropdown();

        return view('groups.usersEdit', compact('users', 'group'));
    }

    public function edit($id)
    {
        $group = $this->groupRepository->show($id);
        $users = $this->userRepository->dropdown();

        return view('groups.edit', compact('users', 'group'));
    }

    public function competitions()
    {
        $groups = $this->groupRepository->all();

        $groupsWithCompetitions = $groups->map(function ($group) {
            // Get last finished competition
            $lastFinished = $this->competitionRepository->getByGroup($group->id)
                ->where('status', CompetitionStatus::FINISHED)
                ->orderBy('end_at', 'desc')
                ->first();

            // Get active competitions
            $activeCompetitions = $this->competitionRepository->getByGroup($group->id)
                ->where('status', CompetitionStatus::ACTIVE)
                ->orderBy('start_at', 'desc')
                ->get();

            // Get next pending competition
            $nextPending = $this->competitionRepository->getByGroup($group->id)
                ->where('status', CompetitionStatus::PENDING)
                ->orderBy('start_at', 'asc')
                ->first();

            return [
                'group' => $group,
                'lastFinished' => $lastFinished,
                'activeCompetitions' => $activeCompetitions,
                'nextPending' => $nextPending,
            ];
        });

        return view('groups.competitions', compact('groupsWithCompetitions'));
    }
}
