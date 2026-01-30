<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuizExampleExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'Math Quiz',
                '2025-01-15',
                'https://example.com/math-help',
                'What is 2 + 2?',
                10,
                '3',
                '4',
                '5',
                '6',
                2,
            ],
            [
                'Math Quiz',
                '2025-01-15',
                '',
                'What is 5 x 3?',
                10,
                '8',
                '15',
                '12',
                '20',
                2,
            ],
            [
                'Science Quiz',
                '2025-01-20',
                'https://example.com/science-help',
                'What is the chemical symbol for water?',
                15,
                'H2O',
                'CO2',
                'O2',
                'N2',
                1,
            ],
            [
                'Science Quiz',
                '2025-01-20',
                '',
                'What planet is closest to the sun?',
                15,
                'Mercury',
                'Venus',
                'Earth',
                'Mars',
                1,
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'quiz_name',
            'date',
            'help',
            'question',
            'points',
            'answer_1',
            'answer_2',
            'answer_3',
            'answer_4',
            'correct',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4CAF50'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // quiz_name
            'B' => 15,  // date
            'C' => 35,  // help
            'D' => 40,  // question
            'E' => 10,  // points
            'F' => 15,  // answer_1
            'G' => 15,  // answer_2
            'H' => 15,  // answer_3
            'I' => 15,  // answer_4
            'J' => 10,  // correct
        ];
    }
}
