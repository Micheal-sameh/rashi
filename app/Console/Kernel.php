<?php

namespace App\Console;

use App\Jobs\CheckCompetitionsJob;
use App\Jobs\CheckTodaysQuizAnswersJob;
use App\Jobs\DeleteOldNotificationsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new CheckCompetitionsJob)->dailyAt('17:00');
        $schedule->job(new CheckTodaysQuizAnswersJob)->dailyAt('17:00');
        $schedule->job(new DeleteOldNotificationsJob)->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
