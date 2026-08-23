<?php

namespace App\Console\Commands;

use App\Jobs\CheckCompetitionsJob;
use Illuminate\Console\Command;

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
        CheckCompetitionsJob::dispatchSync();

        return 0;
    }
}
