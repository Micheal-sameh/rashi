<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Events\OrderReceived;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected FirebaseService $firebaseService
    ) {}

    public function handle($event)
    {
        if ($event instanceof OrderCreated) {
            $this->sendOrderCreatedNotification($event->order);
        } elseif ($event instanceof OrderReceived) {
            $this->sendOrderReceivedNotification($event->order);
        } elseif ($event instanceof OrderCancelled) {
            $this->sendOrderCancelledNotification($event->order);
        }
    }

    private function sendOrderCreatedNotification($order)
    {
        $user = $order->user;
        $servant = $order->servant;

        $title = 'New Order Created';
        $body = "Order #{$order->id} has been created for {$order->reward->name}.";

        // Send to user
        if ($user && $user->fcmTokens->isNotEmpty()) {
            foreach ($user->fcmTokens as $token) {
                $this->firebaseService->sendToDevice($token->token, $title, $body);
            }
        }

        // Send to servant
        if ($servant && $servant->fcmTokens->isNotEmpty()) {
            foreach ($servant->fcmTokens as $token) {
                $this->firebaseService->sendToDevice($token->token, $title, $body);
            }
        }
    }

    private function sendOrderReceivedNotification($order)
    {
        $user = $order->user;
        $servant = $order->servant;

        $title = 'Order Received';
        $body = "Order #{$order->id} has been received: reward {$order->reward->name}.";

        // Send to user
        if ($user && $user->fcmTokens->isNotEmpty()) {
            foreach ($user->fcmTokens as $token) {
                $this->firebaseService->sendToDevice($token->token, $title, $body);
            }
        }

        // Send to servant
        if ($servant && $servant->fcmTokens->isNotEmpty()) {
            foreach ($servant->fcmTokens as $token) {
                $this->firebaseService->sendToDevice($token->token, $title, $body);
            }
        }
    }

    private function sendOrderCancelledNotification($order)
    {
        $user = $order->user;
        $servant = $order->servant;

        $title = 'Order Cancelled';
        $body = "Order #{$order->id} has been cancelled for reward: {$order->reward->name}..";

        // Send to user
        if ($user && $user->fcmTokens->isNotEmpty()) {
            foreach ($user->fcmTokens as $token) {
                $this->firebaseService->sendToDevice($token->token, $title, $body);
            }
        }

        // Send to servant
        if ($servant && $servant->fcmTokens->isNotEmpty()) {
            foreach ($servant->fcmTokens as $token) {
                $this->firebaseService->sendToDevice($token->token, $title, $body);
            }
        }
    }
}
