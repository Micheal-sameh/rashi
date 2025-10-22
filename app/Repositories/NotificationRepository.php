<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Models\UserNotification;

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

    public function createUserNotification(array $data): UserNotification
    {
        return UserNotification::create($data);
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

        $this->createUserNotification([
            'user_id' => $userId,
            'notification_id' => $notification->id,
        ]);

        return $notification;
    }
}
