<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reward_id',
        'quantity',
        'points',
        'status',
        'user_id',
        'servant_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function servant()
    {
        return $this->belongsTo(User::class, 'servant_id');
    }
}
