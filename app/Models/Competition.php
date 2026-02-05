<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Competition extends Model implements HasMedia
{
    use Auditable, HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'start_at',
        'end_at',
        'status',
        'image',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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

    /**
     * Scope for eager loading common API relationships
     */
    public function scopeWithApiRelations(Builder $query): Builder
    {
        return $query->with(['media']);
    }

    /**
     * Scope for eager loading full competition data with quizzes
     */
    public function scopeWithFullData(Builder $query): Builder
    {
        return $query->with([
            'groups',
            'quizzes.questions.answers',
            'quizzes.questions.userAnswers.user',
            'quizzes.questions.userAnswers.answer',
            'media',
        ]);
    }
}
