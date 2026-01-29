<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('orders');
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'user_name' => $this->order->user->name ?? 'N/A',
                'membership_code' => $this->order->user->membership_code ?? 'N/A',
                'reward_name' => $this->order->reward->name ?? 'N/A',
                'status' => $this->order->status,
                'created_at' => $this->order->created_at->format('Y-m-d H:i:s'),
            ],
        ];
    }
}
