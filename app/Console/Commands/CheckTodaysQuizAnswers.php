<?php

namespace App\Console\Commands;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\Quiz;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTodaysQuizAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-todays-quiz-answers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users who have not answered today\'s quiz in active competitions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking users who have not answered today\'s quiz in active competitions...');

        // Get today's quizzes in active competitions
        $todaysQuizzes = Quiz::whereDate('date', today())
            ->whereHas('competition', function ($query) {
                $query->where('status', CompetitionStatus::ACTIVE);
            })
            ->with(['competition.groups.users', 'questions'])
            ->get();

        if ($todaysQuizzes->isEmpty()) {
            $this->info('No quizzes scheduled for today in active competitions.');

            return;
        }

        $this->info('Found '.$todaysQuizzes->count().' quiz(es) for today.');

        foreach ($todaysQuizzes as $quiz) {
            $this->info("Checking quiz: {$quiz->name} (Competition: {$quiz->competition->name})");

            $totalQuestions = $quiz->questions->count();
            $this->info("Total questions: {$totalQuestions}");

            // Get all users in the groups associated with this competition
            $usersInGroups = $quiz->competition->groups->flatMap->users->unique('id');

            $usersNotAnswered = [];

            foreach ($usersInGroups as $user) {
                $answeredQuestions = DB::table('user_answers')
                    ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                    ->where('quiz_questions.quiz_id', $quiz->id)
                    ->where('user_answers.user_id', $user->id)
                    ->distinct('quiz_questions.id')
                    ->count();

                if ($answeredQuestions < $totalQuestions) {
                    $usersNotAnswered[] = $user->name." (answered {$answeredQuestions}/{$totalQuestions})";
                }
            }

            if (! empty($usersNotAnswered)) {
                $this->info('Users who have not fully answered this quiz:');
                foreach ($usersNotAnswered as $user) {
                    $this->line("  - {$user}");
                }
            } else {
                $this->info('All users in this competition have fully answered this quiz.');
            }
        }

        $this->info('Check completed.');
    }
}
