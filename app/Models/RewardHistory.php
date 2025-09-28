<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
