<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Quiz extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'name',
        'date',
        'competition_id',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function pointHistories()
    {
        return $this->morphMany(PointHistory::class, 'subject');
    }

    public function isSolved()
    {
        return $this->pointHistories()->where('user_id', Auth::id());
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
