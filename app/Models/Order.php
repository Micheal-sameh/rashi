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

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', \App\Enums\OrderStatus::PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', \App\Enums\OrderStatus::COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', \App\Enums\OrderStatus::CANCELLED);
    }

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
