<?php

namespace App\Listeners;

use App\Events\FirebaseNotificationSent;
use App\Services\FcmTokenService;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendFirebaseNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected FirebaseService $firebaseService,
        protected FcmTokenService $fcmTokenService
    ) {}

    public function handle(FirebaseNotificationSent $event)
    {
        $this->sendFirebaseNotificationToUsers(
            $event->userIds,
            $event->title,
            $event->body,
            $event->data
        );
    }

    /**
     * Send Firebase push notification to multiple users
     */
    protected function sendFirebaseNotificationToUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        try {
            $allTokens = [];

            foreach ($userIds as $userId) {
                $tokens = $this->fcmTokenService->getTokensByUserId($userId);
                if ($tokens->isNotEmpty()) {
                    $allTokens = array_merge($allTokens, $tokens->pluck('token')->toArray());
                }
            }

            if (! empty($allTokens)) {
                $this->firebaseService->sendToDevices($allTokens, $title, $body, $data);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the notification creation
            Log::error('Failed to send Firebase notifications to users', [
                'user_ids' => $userIds,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
