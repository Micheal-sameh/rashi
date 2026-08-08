<?php

namespace App\Jobs;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Repositories\CompetitionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckCompetitionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(CompetitionRepository $competitionRepository): void
    {
        Log::info('Check started.');

        $competitionRepository->checkCompetition();

        Log::info('Checking active competitions and users who have not solved them...');

        // Get active competitions
        $activeCompetitions = Competition::where('status', CompetitionStatus::ACTIVE)->with('quizzes.questions')->get();
        Log::info('Active competitions: '.$activeCompetitions->count());

        foreach ($activeCompetitions as $competition) {
            Log::info("Checking competition: {$competition->name}");

            // Get all users in the groups associated with this competition
            $usersInGroups = $competition->groups->flatMap->users->unique('id');

            $totalQuestions = $competition->quizzes->sum(fn ($quiz) => $quiz->questions->count());
            Log::info("Total questions in competition: {$totalQuestions}");

            $usersNotSolved = [];

            foreach ($usersInGroups as $user) {
                $answeredQuestions = DB::table('user_answers')
                    ->join('quiz_questions', 'user_answers.quiz_question_id', '=', 'quiz_questions.id')
                    ->join('quizzes', 'quiz_questions.quiz_id', '=', 'quizzes.id')
                    ->where('quizzes.competition_id', $competition->id)
                    ->where('user_answers.user_id', $user->id)
                    ->distinct('quiz_questions.id')
                    ->count();

                if ($answeredQuestions < $totalQuestions) {
                    $usersNotSolved[] = $user->name." (answered {$answeredQuestions}/{$totalQuestions})";
                }
            }

            if (! empty($usersNotSolved)) {
                Log::info('Users who have not solved this competition:', $usersNotSolved);
                // Here you can add logic to send notifications to these users
            } else {
                Log::info('All users in this competition have solved it.');
            }
        }

        Log::info('Check completed.');
    }
}
