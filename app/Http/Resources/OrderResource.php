<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reward' => $this->whenLoaded('reward', fn () => new RewardResource($this->reward)),
            'quantity' => $this->quantity,
            'points' => $this->points,
            'status' => new EnumResource($this->status, OrderStatus::class),
            'servant' => $this->whenLoaded('servant', fn () => $this->servant->name),
        ];
    }
}
