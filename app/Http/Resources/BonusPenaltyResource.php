<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BonusPenaltyResource extends JsonResource
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
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'type' => new EnumResource($this->type, \App\Enums\BonusPenaltyType::class),
            'points' => $this->points,
            'reason' => $this->reason,
            'creator' => $this->whenLoaded('creator', fn () => $this->creator->name),
            'created_at' => $this->created_at,
        ];
    }
}
