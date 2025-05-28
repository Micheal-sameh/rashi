<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Returns extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'reward_id',
        'created_by',
        'quantity',
        'points',
    ];

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public static function addRecord($order)
    {
        return Returns::create([
            'created_by' => Auth::id(),
            'points' => $order->points,
            'reward_id' => $order->reward_id,
            'order_id' => $order->id,
            'quantity' => $order->quantity,
        ]);
    }
}
