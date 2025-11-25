<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'points',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function userAnswer()
    {
        return $this->belongsTo(UserAnswer::class, 'id', 'quiz_question_id');
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
