<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'subject_type',
        'subject_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
