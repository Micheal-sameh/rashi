<?php

namespace App\Services;

use App\Repositories\CompetitionRepository;
use App\Repositories\GroupRepository;

class GroupService
{
    public function __construct(
        protected GroupRepository $groupRepository,
        protected CompetitionRepository $competitionRepository,
    ) {}

    public function getCompetitionsFlowchartData(?int $groupId = null): array
    {
        $groups = $this->groupRepository->all();

        $selectedGroup = null;
        $competitions = collect();

        if ($groupId) {
            $selectedGroup = $this->groupRepository->findById($groupId);
            $competitions = $this->competitionRepository->getByGroup($groupId)
                ->orderBy('start_at', 'asc')
                ->get();
        }

        return compact('groups', 'selectedGroup', 'competitions');
    }
}
