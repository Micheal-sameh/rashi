<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportScheduleLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'status',
        'row_count',
        'error',
        'ran_at',
    ];

    protected $casts = [
        'status' => ScheduleStatus::class,
        'ran_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ReportSchedule::class, 'schedule_id');
    }
}
