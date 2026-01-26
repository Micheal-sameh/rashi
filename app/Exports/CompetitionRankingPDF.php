<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class CompetitionRankingPDF
{
    protected $competition;

    protected $group;

    public function __construct($competition, $group)
    {
        $this->competition = $competition;
        $this->group = $group;
    }

    public function generate()
    {
        $rankings = $this->calculateRankings();

        // Render the view to HTML
        $html = view('exports.competition-ranking', [
            'competition' => $this->competition,
            'group' => $this->group,
            'rankings' => $rankings,
        ])->render();

        // Create mPDF instance with Arabic support (same config as working PDFs)
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'arial',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf;
    }

    protected function calculateRankings()
    {
        $users = $this->group->users;
        $rankings = [];

        foreach ($users as $user) {
            $totalPoints = DB::table('user_answers')
                ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                ->join('quizzes', 'quiz_questions.quiz_id', '=', 'quizzes.id')
                ->where('quizzes.competition_id', $this->competition->id)
                ->where('user_answers.user_id', $user->id)
                ->sum('user_answers.points');

            $quizzesSolved = DB::table('user_answers')
                ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                ->join('quizzes', 'quiz_questions.quiz_id', '=', 'quizzes.id')
                ->where('quizzes.competition_id', $this->competition->id)
                ->where('user_answers.user_id', $user->id)
                ->distinct('quizzes.id')
                ->count('quizzes.id');

            $rankings[] = [
                'user' => $user,
                'total_points' => $totalPoints,
                'quizzes_solved' => $quizzesSolved,
            ];
        }

        // Sort by total points descending
        usort($rankings, function ($a, $b) {
            return $b['total_points'] - $a['total_points'];
        });

        // Add rank
        foreach ($rankings as $index => &$ranking) {
            $ranking['rank'] = $index + 1;
        }

        return $rankings;
    }
}
