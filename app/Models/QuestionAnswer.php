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

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'question_answer_id');
    }

    public function isUserAnswer(): bool
    {
        return UserAnswer::where('question_answer_id', $this->id)->where('user_id', auth()->id())->exists();
    }
}
