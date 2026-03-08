<?php

namespace App\Services;

use App\Enums\CompetitionStatus;
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
            $competitions = $this->fetchGroupCompetitions($groupId);
        }

        return compact('groups', 'selectedGroup', 'competitions');
    }

    public function getCompetitionsFlowchartPdfData(int $groupId): array
    {
        $selectedGroup = $this->groupRepository->findById($groupId);
        $competitions = $this->fetchGroupCompetitions($groupId);

        $counts = [
            'finished' => $competitions->where('status', CompetitionStatus::FINISHED)->count(),
            'active' => $competitions->where('status', CompetitionStatus::ACTIVE)->count(),
            'cancelled' => $competitions->where('status', CompetitionStatus::CANCELLED)->count(),
            'future' => $competitions->where('status', CompetitionStatus::PENDING)->count(),
        ];

        $chartData = $this->buildPdfChartData($competitions);
        $generatedAt = now()->format('Y-m-d H:i');

        return compact('selectedGroup', 'competitions', 'counts', 'chartData', 'generatedAt');
    }

    private function fetchGroupCompetitions(int $groupId)
    {
        return $this->competitionRepository->getByGroup($groupId)
            ->select(['competitions.id', 'competitions.name', 'competitions.start_at', 'competitions.end_at', 'competitions.status'])
            ->orderBy('start_at', 'asc')
            ->get();
    }

    private function buildPdfChartData($competitions): array
    {
        $stages = $competitions->groupBy(fn ($competition) => optional($competition->start_at)->format('Y-m-d'))->values();

        $nodeWidth = 270;
        $nodeHeight = 88;
        $gapX = 110;
        $gapY = 20;
        $rowGap = 90;
        $paddingX = 30;
        $paddingY = 50;
        $maxStagesPerRow = 4;

        $stagesCount = count($stages);
        $rowsCount = max((int) ceil($stagesCount / $maxStagesPerRow), 1);

        $stageHeights = $stages->map(fn ($stage) => (count($stage) * $nodeHeight) + (max(count($stage) - 1, 0) * $gapY));
        $maxStageHeight = max((int) ($stageHeights->max() ?? $nodeHeight), $nodeHeight);

        $longestRowStages = min($maxStagesPerRow, $stagesCount ?: 1);
        $chartWidth = max((int) (($longestRowStages * $nodeWidth) + (max($longestRowStages - 1, 0) * $gapX) + ($paddingX * 2)), 1000);
        $chartHeight = max((int) (($rowsCount * $maxStageHeight) + (max($rowsCount - 1, 0) * $rowGap) + ($paddingY * 2)), 340);

        $nodes = [];
        $edges = [];
        $stageAnchors = [];

        foreach ($stages as $stageIndex => $stage) {
            $rowIndex = (int) floor($stageIndex / $maxStagesPerRow);
            $offsetInRow = $stageIndex % $maxStagesPerRow;
            $isReverseRow = $rowIndex % 2 === 1;
            $colIndex = $isReverseRow ? ($maxStagesPerRow - 1 - $offsetInRow) : $offsetInRow;

            $stageHeight = $stageHeights[$stageIndex];
            $rowTop = $paddingY + ($rowIndex * ($maxStageHeight + $rowGap));
            $startY = (int) ($rowTop + (($maxStageHeight - $stageHeight) / 2));
            $x = (int) ($paddingX + ($colIndex * ($nodeWidth + $gapX)));

            $stageAnchors[$stageIndex] = [
                'row' => $rowIndex,
                'col' => $colIndex,
                'isReverse' => $isReverseRow,
            ];

            foreach ($stage->values() as $nodeIndex => $competition) {
                $y = (int) ($startY + ($nodeIndex * ($nodeHeight + $gapY)));
                $styles = $this->statusStyle($competition->status);
                [$lineOne, $lineTwo] = $this->splitNameLines($competition->name);

                $nodes[$stageIndex][] = [
                    'id' => $competition->id,
                    'x' => $x,
                    'y' => $y,
                    'fill' => $styles['fill'],
                    'stroke' => $styles['stroke'],
                    'hdr' => $styles['hdr'],
                    'txt' => $styles['txt'],
                    'statusLabel' => $styles['label'],
                    'lineOne' => $lineOne,
                    'lineTwo' => $lineTwo,
                    'dateText' => optional($competition->start_at)->format('Y-m-d').' → '.optional($competition->end_at)->format('Y-m-d'),
                ];
            }
        }

        for ($stageIndex = 1; $stageIndex < count($nodes); $stageIndex++) {
            $fromAnchor = $stageAnchors[$stageIndex - 1];
            $toAnchor = $stageAnchors[$stageIndex];
            $sameRow = $fromAnchor['row'] === $toAnchor['row'];

            foreach (($nodes[$stageIndex - 1] ?? []) as $from) {
                foreach (($nodes[$stageIndex] ?? []) as $to) {
                    if ($sameRow) {
                        $fromToRight = $to['x'] >= $from['x'];
                        $fromX = $fromToRight ? ($from['x'] + $nodeWidth) : $from['x'];
                        $toX = $fromToRight ? $to['x'] : ($to['x'] + $nodeWidth);
                        $fromY = $from['y'] + ($nodeHeight / 2);
                        $toY = $to['y'] + ($nodeHeight / 2);
                        $curveOffset = 42;

                        $edges[] = [
                            'd' => sprintf(
                                'M%s,%s C%s,%s %s,%s %s,%s',
                                $fromX,
                                $fromY,
                                $fromToRight ? $fromX + $curveOffset : $fromX - $curveOffset,
                                $fromY,
                                $fromToRight ? $toX - $curveOffset : $toX + $curveOffset,
                                $toY,
                                $toX,
                                $toY
                            ),
                        ];

                        continue;
                    }

                    $fromX = $from['x'] + ($nodeWidth / 2);
                    $fromY = $from['y'] + $nodeHeight;
                    $toX = $to['x'] + ($nodeWidth / 2);
                    $toY = $to['y'];
                    $midYUp = $fromY + 28;
                    $midYDown = $toY - 28;

                    $edges[] = [
                        'd' => sprintf(
                            'M%s,%s C%s,%s %s,%s %s,%s',
                            $fromX,
                            $fromY,
                            $fromX,
                            $midYUp,
                            $toX,
                            $midYDown,
                            $toX,
                            $toY
                        ),
                    ];
                }
            }
        }

        return [
            'stagesCount' => $stagesCount,
            'rowsCount' => $rowsCount,
            'maxStagesPerRow' => $maxStagesPerRow,
            'nodeWidth' => $nodeWidth,
            'nodeHeight' => $nodeHeight,
            'paddingX' => $paddingX,
            'gapX' => $gapX,
            'chartWidth' => $chartWidth,
            'chartHeight' => $chartHeight,
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }

    private function statusStyle(int $status): array
    {
        return match ($status) {
            CompetitionStatus::FINISHED => ['fill' => '#f0fdf4', 'stroke' => '#22c55e', 'hdr' => '#dcfce7', 'txt' => '#15803d', 'label' => 'Finished'],
            CompetitionStatus::ACTIVE => ['fill' => '#fefce8', 'stroke' => '#eab308', 'hdr' => '#fef9c3', 'txt' => '#a16207', 'label' => 'Active'],
            CompetitionStatus::CANCELLED => ['fill' => '#fff1f2', 'stroke' => '#f43f5e', 'hdr' => '#ffe4e6', 'txt' => '#be123c', 'label' => 'Cancelled'],
            default => ['fill' => '#eff6ff', 'stroke' => '#3b82f6', 'hdr' => '#dbeafe', 'txt' => '#1d4ed8', 'label' => 'Upcoming'],
        };
    }

    private function splitNameLines(string $name): array
    {
        $safeName = trim($name);
        if (mb_strlen($safeName) > 56) {
            $safeName = mb_substr($safeName, 0, 56).'…';
        }

        $words = preg_split('/\s+/', $safeName);
        $lineOne = '';
        $lineTwo = '';

        foreach ($words as $word) {
            if (mb_strlen(trim($lineOne.' '.$word)) <= 28) {
                $lineOne = trim($lineOne.' '.$word);
            } elseif (mb_strlen(trim($lineTwo.' '.$word)) <= 28) {
                $lineTwo = trim($lineTwo.' '.$word);
            }
        }

        if ($lineOne === '') {
            $lineOne = mb_substr($safeName, 0, 28);
        }

        if ($lineTwo === '' && mb_strlen($safeName) > 28) {
            $lineTwo = mb_substr($safeName, 28, 28);
        }

        return [$lineOne, $lineTwo];
    }
}
