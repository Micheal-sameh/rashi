<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        $shuffledAnswers = $this->whenLoaded('answers', function () {
            return $this->answers->shuffle();
        });

        return [
            'id' => $this->id,
            'question' => $this->question,
            'points' => $this->points,
            'is_correct' => (Carbon::parse($this->quiz->date)->lt(today())) ? true : false,
            'answers' => (Carbon::parse($this->quiz->date)->lt(today()))
            ? ($shuffledAnswers ? ModelAnswerResource::collection($shuffledAnswers) : null)
            : ($shuffledAnswers ? AnswerResource::collection($shuffledAnswers) : null),
        ];
    }
}
