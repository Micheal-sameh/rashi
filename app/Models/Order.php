<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Auditable, HasFactory;

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

    /**
     * Scope for eager loading common API relationships
     */
    public function scopeWithApiRelations(Builder $query): Builder
    {
        return $query->with([
            'reward.media',
            'user.media',
            'servant:id,name',
        ]);
    }
}
