<?php

namespace App\Models;

use App\Enums\BonusPenaltyType;
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
        $user = null;
        if ($data['subject'] instanceof Quiz) {
            $type = get_class($data['subject']);
            $subject_id = $data['subject']->id;
            $points = $data['score'];
            $score = $data['score'];
        } elseif ($data instanceof BonusPenalty) {
            $type = get_class($data);
            $subject_id = $data->id;
            $points = ($data->type == BonusPenaltyType::BONUS || $data->type == BonusPenaltyType::WELCOME_BONUS) ? $data->points : -1 * $data->points; // bonus positive, penalty negative
            $score = 0;
            $user = $data->user;
        } else {
            $type = get_class($data);
            $subject_id = $data->id;
            $points = ($data instanceof Order) ? -1 * $data->points : $data->points;
            $score = 0;
        }

        $user = $user ?? Auth::user();
        PointHistory::create([
            'user_id' => $user->id,
            'amount' => abs($points),
            'points' => $user->points + $points,
            'score' => $user->score + $score,
            'subject_id' => $subject_id,
            'subject_type' => $type,
        ]);
    }

    public function getTypeAttribute()
    {
        return match ($this->subject_type) {
            BonusPenalty::class => in_array($this->subject->type, [BonusPenaltyType::BONUS, BonusPenaltyType::WELCOME_BONUS]) ? 'Bonus' : 'Penalty',
            Order::class => 'Redeem',
            Quiz::class => 'Quiz',
            Returns::class => 'Return',
            default => 'debit',
        };
    }
}
