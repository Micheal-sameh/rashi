<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $model)
    {
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

        return $notification;
    }
}
