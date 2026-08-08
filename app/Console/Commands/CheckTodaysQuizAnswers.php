<?php

namespace App\Console\Commands;

use App\Jobs\CheckTodaysQuizAnswersJob;
use Illuminate\Console\Command;

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
        CheckTodaysQuizAnswersJob::dispatchSync();

        return 0;
    }
}
