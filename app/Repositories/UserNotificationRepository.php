<?php

namespace App\Repositories;

use App\Models\UserNotification;

class UserNotificationRepository extends BaseRepository
{
    public function __construct(UserNotification $model)
    {
        parent::__construct($model);
    }

    protected function model(): string
    {
        return UserNotification::class;
    }

    public function createUserNotification(array $data)
    {
        return $this->model->create($data);
    }

    public function getUserNotifications(int $limit = 10)
    {
        return $this->model->with('notification.subject')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function markUserNotificationAsRead(int $notificationId): bool
    {
        $userNotification = $this->model->where('id', $notificationId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $userNotification) {
            return false;
        }

        return $userNotification->markAsRead();
    }

    public function markAllUserNotificationsAsRead(): int
    {
        return $this->model->where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}
