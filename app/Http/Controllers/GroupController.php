<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupUsers;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;

class GroupController extends Controller
{
    public function __construct(
        protected GroupRepository $groupRepository,
        protected UserRepository $userRepository,
    ) {}

    public function index()
    {
        $groups = $this->groupRepository->index();

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $users = $this->userRepository->dropdown();

        return view('groups.create', compact('users'));
    }

    public function store(CreateGroupRequest $request)
    {
        $this->groupRepository->store($request->name, $request->users);

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
}
