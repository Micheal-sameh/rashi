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
        'reward_id',
    ];

    public static function addRecord($object, $quantity = null)
    {
        $reward_id = match (get_class($object)) {
            Reward::class => $object->id,
            Order::class => $object->reward->id,
            Returns::class => $object->reward->id,
            default => null
        };

        RewardHistory::create([
            'quantity' => $quantity ?: $object->quantity,
            'points' => $object->points,
            'subject_id' => $object->id,
            'subject_type' => get_class($object),
            'created_by' => Auth::id(),
            'reward_id' => $reward_id,
        ]);
    }
}
