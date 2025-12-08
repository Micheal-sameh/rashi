<?php

namespace App\Console\Commands;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Repositories\CompetitionRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckCompetitions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-competitions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check active competitions and users who have not solved them daily at 5 PM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Check started.');

        $competitionRepository = app(CompetitionRepository::class);
        $competitionRepository->checkCompetition();

        $this->info('Checking active competitions and users who have not solved them...');

        // Get active competitions
        $activeCompetitions = Competition::where('status', CompetitionStatus::ACTIVE)->with('quizzes.questions')->get();
        $this->info('Active competitions: '.$activeCompetitions->count());

        foreach ($activeCompetitions as $competition) {
            $this->info("Checking competition: {$competition->name}");

            // Get all users in the groups associated with this competition
            $usersInGroups = $competition->groups->flatMap->users->unique('id');

            $totalQuestions = $competition->quizzes->sum(fn ($quiz) => $quiz->questions->count());
            $this->info("Total questions in competition: {$totalQuestions}");

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
                $this->info('Users who have not solved this competition:');
                foreach ($usersNotSolved as $user) {
                    $this->line("  - {$user}");
                }
                // Here you can add logic to send notifications to these users
            } else {
                $this->info('All users in this competition have solved it.');
            }
        }

        $this->info('Check completed.');
    }
}
