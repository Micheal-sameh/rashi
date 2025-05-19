<?php

namespace App\Http\Resources;

use App\Enums\CompetitionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionResource extends JsonResource
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
            'question' => $this->question,
            'points' => $this->points,
            'answers' => ($this->quiz->competition->status == CompetitionStatus::FINISHED)
            ? $this->whenloaded('answers', ModelAnswerResource::collection($this->answers))
            : $this->whenloaded('answers', AnswerResource::collection($this->answers)),
        ];
    }
}
