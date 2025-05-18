<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Repositories\GroupRepository;

class GroupController extends Controller
{
    public function __construct(protected GroupRepository $groupRepository) {}

    public function index()
    {
        $groups = $this->groupRepository->index();

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(CreateGroupRequest $request)
    {
        $this->groupRepository->store($request->name);

        return redirect()->route('groups.index')->with('success', 'Group created successfully');
    }

    public function update($id, CreateGroupRequest $request)
    {
        $group = $this->groupRepository->update($id, $request->name);

        return redirect()->route('groups.index')->with('success', "Group $group->name updated successfully");
    }
}
