<?php

namespace App\Jobs;

use App\Models\PointTransfer;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPointTransferNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transfer;

    protected $senderId;

    protected $receiverId;

    /**
     * Create a new job instance.
     */
    public function __construct(PointTransfer $transfer, int $senderId, int $receiverId)
    {
        $this->transfer = $transfer;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseService $firebaseService): void
    {
        try {
            // Load users
            $sender = User::find($this->senderId);
            $receiver = User::find($this->receiverId);

            if (! $sender || ! $receiver) {
                Log::warning('Point transfer notification skipped: User not found', [
                    'transfer_id' => $this->transfer->id,
                    'sender_id' => $this->senderId,
                    'receiver_id' => $this->receiverId,
                ]);

                return;
            }

            // Send notification to sender
            $senderTokens = $sender->fcmTokens()->pluck('token')->toArray();
            if (! empty($senderTokens)) {
                $firebaseService->sendToDevices(
                    $senderTokens,
                    __('messages.points_sent'),
                    __('messages.points_sent_message', [
                        'points' => $this->transfer->points,
                        'receiver' => $receiver->name,
                    ]),
                    [
                        'type' => 'point_transfer_sent',
                        'transfer_id' => $this->transfer->id,
                        'points' => $this->transfer->points,
                        'receiver_name' => $receiver->name,
                        'receiver_id' => $receiver->id,
                    ]
                );
            }

            // Send notification to receiver
            $receiverTokens = $receiver->fcmTokens()->pluck('token')->toArray();
            if (! empty($receiverTokens)) {
                $firebaseService->sendToDevices(
                    $receiverTokens,
                    __('messages.points_received'),
                    __('messages.points_received_message', [
                        'points' => $this->transfer->points,
                        'sender' => $sender->name,
                    ]),
                    [
                        'type' => 'point_transfer_received',
                        'transfer_id' => $this->transfer->id,
                        'points' => $this->transfer->points,
                        'sender_name' => $sender->name,
                        'sender_id' => $sender->id,
                    ]
                );
            }

            Log::info('Point transfer notifications sent successfully', [
                'transfer_id' => $this->transfer->id,
                'sender_tokens' => count($senderTokens),
                'receiver_tokens' => count($receiverTokens),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send point transfer notifications', [
                'transfer_id' => $this->transfer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Don't fail the job, just log the error
            // We don't want to retry notifications multiple times
        }
    }
}
