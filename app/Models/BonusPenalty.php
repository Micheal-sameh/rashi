<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BonusPenalty extends Model
{
    use HasFactory;

    protected $table = 'bonuses_penalties';

    protected $fillable = [
        'user_id',
        'type',
        'points',
        'reason',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function addRecord($data)
    {
        $bonusPenalty = BonusPenalty::create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'points' => $data['points'],
            'reason' => $data['reason'],
            'created_by' => Auth::id(),
        ]);

        PointHistory::addRecord($bonusPenalty);

        return $bonusPenalty;
    }
}
