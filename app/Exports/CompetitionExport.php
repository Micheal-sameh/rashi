<?php

namespace App\Exports;

use App\Models\Competition;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompetitionExport implements FromArray, WithHeadings, WithStyles
{
    protected $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function array(): array
    {
        $data = [];

        // Load all quizzes with their questions and answers
        $quizzes = $this->competition->quizzes()
            ->with(['questions.answers'])
            ->orderBy('date')
            ->get();

        foreach ($quizzes as $quiz) {
            foreach ($quiz->questions as $question) {
                // Get answers
                $answers = $question->answers;

                // Find correct answer index
                $correctAnswerIndex = 0;
                foreach ($answers as $index => $answer) {
                    if ($answer->is_correct) {
                        $correctAnswerIndex = $index + 1;
                        break;
                    }
                }

                $data[] = [
                    $quiz->name,
                    Carbon::parse($quiz->date)->format('Y-m-d'),
                    $question->question,
                    $question->points,
                    $answers[0]->answer ?? '',
                    $answers[1]->answer ?? '',
                    $answers[2]->answer ?? '',
                    $answers[3]->answer ?? '',
                    $correctAnswerIndex,
                ];
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'quiz_name',
            'date',
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
            1 => ['font' => ['bold' => true]],
        ];
    }
}
