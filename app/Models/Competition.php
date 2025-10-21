<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Competition extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'start_at',
        'end_at',
        'status',
        'image',
    ];

    protected $mediaAttributes = [
        'image',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'competition_groups');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
