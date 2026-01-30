<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FamilyExport implements FromArray, WithHeadings, WithStyles
{
    protected $membersData;

    protected $familyCode;

    public function __construct($membersData, $familyCode)
    {
        $this->membersData = $membersData;
        $this->familyCode = $familyCode;
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->membersData as $memberData) {
            $user = $memberData['user'];

            $data[] = [
                $user->name ?: $user->membership_code,
                $user->membership_code,
                $memberData['final_score'],
                $memberData['final_points'],
                $memberData['quizzes_solved'].' / '.$memberData['total_quizzes'],
                $memberData['last_quiz']['name'] ?? 'N/A',
                $memberData['last_quiz']['date'] ?? 'N/A',
                $memberData['last_order']['reward_name'] ?? 'N/A',
                $memberData['last_order']['date'] ?? 'N/A',
                $memberData['last_bonus']['value'] ?? 'N/A',
                $memberData['last_bonus']['date'] ?? 'N/A',
                $memberData['last_penalty']['value'] ?? 'N/A',
                $memberData['last_penalty']['date'] ?? 'N/A',
                $memberData['last_competition']['name'] ?? 'N/A',
                $memberData['last_competition']['date'] ?? 'N/A',
                $memberData['groups']->pluck('name')->join(', '),
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Membership Code',
            'Final Score',
            'Final Points',
            'Quizzes Solved',
            'Last Quiz Name',
            'Last Quiz Date',
            'Last Reward',
            'Last Reward Date',
            'Last Bonus',
            'Last Bonus Date',
            'Last Penalty',
            'Last Penalty Date',
            'Last Competition',
            'Last Competition Date',
            'Groups',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
