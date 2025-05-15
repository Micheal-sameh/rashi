<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'correct_answers_count' => $this['correct_answers'],
            'score' => $this['score'],
            'total_questions' => $this['total_questions'],
        ];
    }
}
