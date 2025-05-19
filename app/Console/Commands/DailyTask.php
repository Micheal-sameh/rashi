<?php

namespace App\Console\Commands;

use App\Repositories\CompetitionRepository;
use Illuminate\Console\Command;

class DailyTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:competitions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $competitionRepository = app(CompetitionRepository::class);
        $competitionRepository->checkCompetitions();
    }
}
