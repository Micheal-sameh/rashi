<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FirebaseNotificationSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $userIds;

    public string $title;

    public string $body;

    public array $data;

    /**
     * Create a new event instance.
     */
    public function __construct(array $userIds, string $title, string $body, array $data = [])
    {
        $this->userIds = $userIds;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }
}
