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

        $title = __('messages.new_order_created');
        $body = __('messages.order_created_body', ['id' => $order->id, 'reward' => $order->reward->name]);

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

        $title = __('messages.order_received');
        $body = __('messages.order_received_body', ['id' => $order->id, 'reward' => $order->reward->name]);

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

        $title = __('messages.order_cancelled');
        $body = __('messages.order_cancelled_body', ['id' => $order->id, 'reward' => $order->reward->name]);

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
