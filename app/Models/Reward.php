<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Reward extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'quantity',
        'status',
        'points',
        'image',
        'group_id',
    ];

    protected $mediaAttributes = [
        'image',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
