<?php

namespace App\Services;

use App\Jobs\SendFcmNotification;
use App\Jobs\SendFcmNotificationToMultipleDevices;
use App\Jobs\SendFcmNotificationToTopic;

class FirebaseService
{
    /**
     * Queue push notification to a single device
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = []): bool
    {
        SendFcmNotification::dispatch($token, $title, $body, $data);

        return true;
    }

    /**
     * Queue push notification to multiple devices
     */
    public function sendToDevices(array $tokens, string $title, string $body, array $data = []): bool
    {
        if (empty($tokens)) {
            return false;
        }

        SendFcmNotificationToMultipleDevices::dispatch($tokens, $title, $body, $data);

        return true;
    }

    /**
     * Queue push notification to a topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        SendFcmNotificationToTopic::dispatch($topic, $title, $body, $data);

        return true;
    }
}
