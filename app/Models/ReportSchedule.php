<?php

namespace App\Models;

use App\Enums\ScheduleFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'frequency',
        'time',
        'day_of_week',
        'day_of_month',
        'recipients',
        'is_active',
        'last_run_at',
    ];

    protected $casts = [
        'frequency' => ScheduleFrequency::class,
        'recipients' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReportScheduleLog::class, 'schedule_id');
    }
}
