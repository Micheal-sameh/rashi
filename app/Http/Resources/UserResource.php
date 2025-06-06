<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'membership_code' => $this->membership_code,
            'phone' => $this->phone,
            'score' => $this->score,
            'point' => $this->point,
            'role' => $this->whenLoaded('roles', fn () => $this->roles->first()->name),
            'profile_image' => $this->getFirstMediaUrl('profile_images') ?: null,
        ];
    }
}
