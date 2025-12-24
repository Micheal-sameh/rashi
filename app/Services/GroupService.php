<?php

namespace App\Services;

use App\Repositories\GroupRepository;

class GroupService
{
    public function __construct(protected GroupRepository $groupRepository) {}

    public function index()
    {
        $groups = $this->groupRepository->index();

        return $groups;
    }

    public function create($input)
    {
        $groupData = $input;
        $group = $this->groupRepository->store($groupData['name'], $groupData['abbreviation'], $groupData['users'] ?? []);

        return $group;
    }

    public function update($id, $input)
    {
        $groupData = $input;
        $group = $this->groupRepository->update($id, $groupData['name'], $groupData['abbreviation']);

        return $group;
    }
}
