<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
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
            'name' => $this->name,
            'price' => (int) $this->points,
            'in_stock' => $this->quantity > 0 ? true : false,
            'image' => $this->getFirstMediaUrl('rewards_images'),
        ];
    }
}
