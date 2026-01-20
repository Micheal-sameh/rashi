<?php

namespace App\Listeners;

use App\Enums\CompetitionStatus;
use App\Events\CompetitionStatusUpdated;
use App\Models\Competition;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCompetitionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected FirebaseService $firebaseService
    ) {}

    public function handle(CompetitionStatusUpdated $event)
    {
        if ($event->newStatus == CompetitionStatus::ACTIVE) {
            $competition = $event->competition;

            // Create the notification first
            $notification = Notification::create([
                'title' => __('messages.competition_activated'),
                'message' => __('messages.competition_activated_body', ['name' => $competition->name]),
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

            // Get users from associated groups
            $users = $competition->groups->flatMap(function ($group) {
                return $group->users;
            })->unique('id');

            foreach ($users as $user) {
                UserNotification::create([
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                ]);

                // Send FCM notification
                $title = __('messages.competition_activated');
                $body = __('messages.competition_activated_body', ['name' => $competition->name]);
                if ($user->fcmTokens->isNotEmpty()) {
                    foreach ($user->fcmTokens as $token) {
                        $this->firebaseService->sendToDevice($token->token, $title, $body);
                    }
                }
            }
        }
    }
}
