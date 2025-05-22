<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RewardHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'points',
        'subject_id',
        'subject_type',
        'created_by',
    ];

    public static function addRecord($reward)
    {
        RewardHistory::create([
            'quantity' => $reward->quantity,
            'points' => $reward->points,
            'subject_id' => $reward->id,
            'subject_type' => get_class($reward),
            'created_by' => Auth::id(),
        ]);
    }
}
