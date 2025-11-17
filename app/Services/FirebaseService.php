<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send push notification to a single device using Kreait Firebase SDK
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->toToken($token)
                ->withNotification($notification)
                ->withData($data);

            $result = $this->messaging->send($message);

            Log::info('Firebase notification sent successfully', [
                'message_id' => $result,
                'token' => $token,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase notification failed', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);

            return false;
        }
    }

    /**
     * Send push notification to multiple devices using Kreait Firebase SDK
     */
    public function sendToDevices(array $tokens, string $title, string $body, array $data = []): bool
    {
        $successCount = 0;

        foreach ($tokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data)) {
                $successCount++;
            }
        }

        return $successCount > 0;
    }

    /**
     * Send push notification to a topic using Kreait Firebase SDK
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->toTopic($topic)
                ->withNotification($notification)
                ->withData($data);

            $result = $this->messaging->send($message);

            Log::info('Firebase topic notification sent successfully', [
                'message_id' => $result,
                'topic' => $topic,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase topic notification failed', [
                'error' => $e->getMessage(),
                'topic' => $topic,
            ]);

            return false;
        }
    }
}
