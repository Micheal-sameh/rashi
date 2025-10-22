<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserNotificationResource;
use App\Repositories\UserNotificationRepository;

class NotificationController extends BaseController
{
    protected $userNotificationRepository;

    public function __construct(UserNotificationRepository $userNotificationRepository)
    {
        $this->userNotificationRepository = $userNotificationRepository;
    }

    public function index()
    {
        $notifications = $this->userNotificationRepository->getUserNotifications();

        return $this->apiResponse(UserNotificationResource::collection($notifications));
    }

    public function markAsRead($id)
    {
        $result = $this->userNotificationRepository->markUserNotificationAsRead($id);

        if (! $result) {
            return $this->apiErrorResponse('Notification not found', 404);
        }

        return $this->apiResponse(null, 'Notification marked as read', 200);
    }

    public function markAllAsRead()
    {
        $this->userNotificationRepository->markAllUserNotificationsAsRead();

        return $this->apiResponse(null, 'All notifications marked as read', 200);
    }
}
