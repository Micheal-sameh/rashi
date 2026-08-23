<?php

namespace App\Jobs;

use App\Enums\CompetitionStatus;
use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckTodaysQuizAnswersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Checking users who have not answered today\'s quiz in active competitions...');

        // Get today's quizzes in active competitions
        $todaysQuizzes = Quiz::whereDate('date', today())
            ->whereHas('competition', function ($query) {
                $query->where('status', CompetitionStatus::ACTIVE);
            })
            ->with(['competition.groups.users', 'questions'])
            ->get();

        if ($todaysQuizzes->isEmpty()) {
            Log::info('No quizzes scheduled for today in active competitions.');

            return;
        }

        Log::info('Found '.$todaysQuizzes->count().' quiz(es) for today.');

        foreach ($todaysQuizzes as $quiz) {
            Log::info("Checking quiz: {$quiz->name} (Competition: {$quiz->competition->name})");

            $totalQuestions = $quiz->questions->count();
            Log::info("Total questions: {$totalQuestions}");

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
                Log::info('Users who have not fully answered this quiz:', $usersNotAnswered);
            } else {
                Log::info('All users in this competition have fully answered this quiz.');
            }
        }

        Log::info('Check completed.');
    }
}
