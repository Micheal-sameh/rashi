<?php

namespace App\Http\Resources;

use App\Enums\CompetitionStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
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
            'start_at' => Carbon::parse($this->start_at)->format('d-m-Y'),
            'end_at' => Carbon::parse($this->end_at)->format('d-m-Y'),
            'status' => new EnumResource($this->status, CompetitionStatus::class),
            'image' => $this->getFirstMediaUrl('competitions_images'),
        ];
    }
}
