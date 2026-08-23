<?php

namespace App\Console\Commands;

use App\Jobs\DeleteOldNotificationsJob;
use Illuminate\Console\Command;

class DeleteOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete automatic notifications older than 90 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DeleteOldNotificationsJob::dispatchSync();

        return 0;
    }
}
