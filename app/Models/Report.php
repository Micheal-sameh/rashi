<?php

namespace App\Models;

use App\Enums\ReportSortDirection;
use App\Traits\HasCreatedUpdatedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasCreatedUpdatedBy, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'table_name',
        'columns',
        'filters',
        'sort_column',
        'sort_direction',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'columns' => 'array',
        'filters' => 'array',
        'sort_direction' => ReportSortDirection::class,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'report_shares', 'report_id', 'user_id')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ReportSchedule::class);
    }

    public function scopeVisibleTo(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $query) use ($userId) {
            $query->where('created_by', $userId)
                ->orWhereHas('sharedWith', function (Builder $query) use ($userId) {
                    $query->where('users.id', $userId);
                });
        });
    }
}
