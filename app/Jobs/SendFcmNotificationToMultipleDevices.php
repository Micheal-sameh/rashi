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

class SendFcmNotificationToMultipleDevices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $tokens,
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

            $successCount = 0;
            foreach ($this->tokens as $token) {
                try {
                    $message = CloudMessage::new()
                        ->toToken($token)
                        ->withNotification($notification)
                        ->withData($this->data);

                    $result = $messaging->send($message);

                    Log::info('Firebase notification sent successfully', [
                        'message_id' => $result,
                        'token' => $token,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    Log::error('Firebase notification failed for token', [
                        'error' => $e->getMessage(),
                        'token' => $token,
                    ]);
                    // Continue with other tokens even if one fails
                }
            }

            Log::info('Batch Firebase notification completed', [
                'total_tokens' => count($this->tokens),
                'successful' => $successCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Firebase batch notification failed', [
                'error' => $e->getMessage(),
                'token_count' => count($this->tokens),
            ]);

            // Retry the job if it fails
            if ($this->attempts() < $this->tries) {
                $this->release(60); // Retry after 60 seconds
            }
        }
    }
}
