<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CompetitionLeaderboardExport implements FromArray, WithHeadings, WithTitle
{
    protected $competition;

    protected $userStats;

    protected $groupRankings;

    public function __construct($competition, array $userStats, array $groupRankings = [])
    {
        $this->competition = $competition;
        $this->userStats = $userStats;
        $this->groupRankings = $groupRankings;
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Name',
            'Group',
            'Total Points',
            'Correct Answers',
            'Total Questions',
        ];
    }

    public function array(): array
    {
        $rows = [];

        $rank = 1;
        foreach ($this->userStats as $stats) {
            $rows[] = [
                $rank++,
                $stats['name'] ?? '',
                $stats['group_name'] ?? 'Unassigned',
                $stats['total_points'] ?? 0,
                $stats['total_correct'] ?? 0,
                $stats['total_questions'] ?? 0,
            ];
        }

        if (! empty($this->groupRankings)) {
            foreach ($this->groupRankings as $groupRanking) {
                $rows[] = [];
                $rows[] = [
                    'Group Ranking',
                    $groupRanking['title'] ?? 'Unassigned',
                    '',
                    '',
                    '',
                    '',
                ];

                $groupRank = 1;
                foreach ($groupRanking['users'] ?? [] as $user) {
                    $rows[] = [
                        $groupRank++,
                        $user['name'] ?? '',
                        $groupRanking['title'] ?? ($user['group_name'] ?? 'Unassigned'),
                        $user['total_points'] ?? 0,
                        $user['total_correct'] ?? 0,
                        $user['total_questions'] ?? 0,
                    ];
                }
            }
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Leaderboard';
    }
}
