<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class SendFcmNotificationToTopic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $topic,
        public string $title,
        public string $body,
        public array $data = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
            $messaging = $factory->createMessaging();

            $notification = Notification::create($this->title, $this->body);

            $message = CloudMessage::new()
                ->toTopic($this->topic)
                ->withNotification($notification)
                ->withData($this->data);

            $result = $messaging->send($message);

            Log::info('Firebase topic notification sent successfully', [
                'message_id' => $result,
                'topic' => $this->topic,
            ]);
        } catch (\Exception $e) {
            Log::error('Firebase topic notification failed', [
                'error' => $e->getMessage(),
                'topic' => $this->topic,
            ]);

            // Retry the job if it fails
            if ($this->attempts() < $this->tries) {
                $this->release(60); // Retry after 60 seconds
            }
        }
    }
}
