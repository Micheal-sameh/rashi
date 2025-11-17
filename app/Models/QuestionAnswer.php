<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_question_id',
        'answer',
        'is_correct',
    ];

    public function isUserAnswer(): bool
    {
        return UserAnswer::where('question_answer_id', $this->id)->where('user_id', auth()->id())->exists();
    }
}
