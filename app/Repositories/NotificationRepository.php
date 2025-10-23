<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Services\FcmTokenService;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class NotificationRepository extends BaseRepository
{
    public function __construct(
        Notification $model,
        protected FirebaseService $firebaseService,
        protected FcmTokenService $fcmTokenService
    ) {
        parent::__construct($model);
    }

    protected function model(): string
    {
        return Notification::class;
    }

    public function createNotification(array $data): Notification
    {
        return $this->model->create($data);
    }

    public function createNotificationForUser(int $userId, string $title, string $message, string $type, string $subjectType, int $subjectId, array $data = []): Notification
    {
        $notification = $this->createNotification([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'data' => $data,
        ]);

        app(UserNotificationRepository::class)->createUserNotification([
            'user_id' => $userId,
            'notification_id' => $notification->id,
        ]);

        // Send Firebase push notification
        $this->sendFirebaseNotification($userId, $title, $message, $data);

        return $notification;
    }

    public function index()
    {
        return $this->model->latest()->paginate();
    }

    public function createNotificationForUsers(array $userIds, string $message, string $type = 'info'): Notification
    {
        $notification = $this->createNotification([
            'title' => 'info',
            'message' => $message,
            'type' => $type,
            'subject_type' => null,
            'subject_id' => null,
            'data' => [],
        ]);

        foreach ($userIds as $userId) {
            app(UserNotificationRepository::class)->createUserNotification([
                'user_id' => $userId,
                'notification_id' => $notification->id,
            ]);
        }

        // Fire event to send Firebase push notifications to all users
        event(new \App\Events\FirebaseNotificationSent($userIds, 'info', $message, []));

        return $notification;
    }

    /**
     * Send Firebase push notification to a single user
     */
    protected function sendFirebaseNotification(int $userId, string $title, string $body, array $data = []): void
    {
        try {
            $tokens = $this->fcmTokenService->getTokensByUserId($userId);

            if ($tokens->isNotEmpty()) {
                $tokenArray = $tokens->pluck('token')->toArray();
                $this->firebaseService->sendToDevices($tokenArray, $title, $body, $data);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the notification creation
            Log::error('Failed to send Firebase notification', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
