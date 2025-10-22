<?php

namespace App\Listeners;

use App\Enums\CompetitionStatus;
use App\Events\CompetitionStatusUpdated;
use App\Models\Competition;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCompetitionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CompetitionStatusUpdated $event)
    {
        if ($event->newStatus == CompetitionStatus::ACTIVE) {
            $competition = $event->competition;

            // Get users from associated groups
            $users = $competition->groups->flatMap(function ($group) {
                return $group->users;
            })->unique('id');

            foreach ($users as $user) {
                UserNotification::create([
                    'user_id' => $user->id,
                    'title' => 'Competition Activated',
                    'message' => "The competition '{$competition->name}' has been activated and is now live!",
                    'type' => 'success',
                    'subject_type' => Competition::class,
                    'subject_id' => $competition->id,
                    'data' => [
                        'competition_id' => $competition->id,
                        'competition_name' => $competition->name,
                        'start_at' => $competition->start_at,
                        'end_at' => $competition->end_at,
                    ],
                ]);
            }
        }
    }
}
