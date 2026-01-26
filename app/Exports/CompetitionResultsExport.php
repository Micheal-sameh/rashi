<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompetitionResultsExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $competition;

    protected $group;

    protected $data;

    protected $quizDates;

    protected $users;

    public function __construct($competition, $group)
    {
        $this->competition = $competition;
        $this->group = $group;
        $this->prepareData();
    }

    protected function prepareData()
    {
        // Get all users in this group
        $this->users = $this->group->users;

        // Get all quizzes for this competition with their dates
        $quizzes = $this->competition->quizzes()->orderBy('date')->get();
        $this->quizDates = $quizzes->pluck('date')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        // Build data array
        $this->data = [];
        $solvedCountsPerQuiz = array_fill(0, count($quizzes), 0);

        foreach ($this->users as $user) {
            $row = [$user->name];
            $totalSolved = 0;

            foreach ($quizzes as $index => $quiz) {
                // Check if user solved this quiz
                $solved = DB::table('user_answers')
                    ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                    ->where('quiz_questions.quiz_id', $quiz->id)
                    ->where('user_answers.user_id', $user->id)
                    ->exists();

                $row[] = $solved ? 'Yes' : 'No';

                if ($solved) {
                    $totalSolved++;
                    $solvedCountsPerQuiz[$index]++;
                }
            }

            $row[] = $totalSolved;
            $this->data[] = $row;
        }

        // Add row for quiz solve counts
        $countRow = ['Solved Count'];
        foreach ($solvedCountsPerQuiz as $count) {
            $countRow[] = $count;
        }
        $countRow[] = ''; // Empty cell for total column
        $this->data[] = $countRow;

        // Add empty row
        $this->data[] = array_fill(0, count($this->quizDates) + 2, '');

        // Add total quizzes row
        $totalQuizzesRow = ['Total Quizzes', count($quizzes)];
        $this->data[] = $totalQuizzesRow;

        // Calculate percentage of solving for the group
        $totalPossibleSolves = count($this->users) * count($quizzes);
        $totalActualSolves = array_sum($solvedCountsPerQuiz);
        $percentage = $totalPossibleSolves > 0 ? round(($totalActualSolves / $totalPossibleSolves) * 100, 2) : 0;

        $percentageRow = ['Group Solve Percentage', $percentage.'%'];
        $this->data[] = $percentageRow;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        $headings = ['User Name'];
        foreach ($this->quizDates as $date) {
            $headings[] = $date;
        }
        $headings[] = 'Total Solved';

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];

        // Style the header row
        $styles[1] = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC'],
            ],
        ];

        // Color cells based on solved/not solved
        $rowIndex = 2; // Start from row 2 (after headers)
        foreach ($this->users as $userIndex => $user) {
            $colIndex = 2; // Start from column B (after user name)

            $quizzes = $this->competition->quizzes()->orderBy('date')->get();
            foreach ($quizzes as $quiz) {
                $solved = DB::table('user_answers')
                    ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                    ->where('quiz_questions.quiz_id', $quiz->id)
                    ->where('user_answers.user_id', $user->id)
                    ->exists();

                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);

                $styles[$columnLetter.$rowIndex] = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $solved ? '90EE90' : 'FF6B6B'], // Green if solved, Red if not
                    ],
                ];

                $colIndex++;
            }
            $rowIndex++;
        }

        // Bold the summary rows
        $summaryStartRow = $rowIndex + 1;
        $styles[$summaryStartRow] = ['font' => ['bold' => true]];
        $styles[$summaryStartRow + 2] = ['font' => ['bold' => true]];
        $styles[$summaryStartRow + 3] = ['font' => ['bold' => true]];

        return $styles;
    }

    public function title(): string
    {
        return substr($this->group->name, 0, 31); // Excel sheet name limit is 31 characters
    }
}
