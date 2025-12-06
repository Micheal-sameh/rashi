<?php

namespace App\Console\Commands;

use App\Repositories\CompetitionRepository;
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
    protected $description = 'Check active competitions and pending items daily at 12 AM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Check started.');

        $competitionREpository = app(CompetitionRepository::class);
        $competitionREpository->checkCompetition();

        $this->info('Check completed.');
    }
}
