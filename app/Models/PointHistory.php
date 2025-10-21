<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PointHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'points',
        'score',
        'subject_id',
        'subject_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public static function addRecord($data)
    {
        if ($data['subject'] instanceof Quiz) {
            $type = get_class($data['subject']);
            $subject_id = $data['subject']->id;
            $points = $data['score'];
            $score = $data['score'];
        } elseif ($data instanceof BonusPenalty) {
            $type = get_class($data);
            $subject_id = $data->id;
            $points = ($data->type == 1) ? $data->points : -1 * $data->points; // bonus positive, penalty negative
            $score = 0;
        } else {
            $type = get_class($data);
            $subject_id = $data->id;
            $points = ($data instanceof Order) ? -1 * $data->points : $data->points;
            $score = 0;
        }

        $user = Auth::user();
        PointHistory::create([
            'user_id' => $user->id,
            'amount' => abs($points),
            'points' => $user->points + $points,
            'score' => $user->score + $score,
            'subject_id' => $subject_id,
            'subject_type' => $type,
        ]);
    }
}
