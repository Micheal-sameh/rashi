<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'user_id',
        'user_name',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function getModelNameAttribute()
    {
        return class_basename($this->model_type);
    }

    public function getChangesAttribute()
    {
        if ($this->action === 'created') {
            return $this->new_values;
        }

        if ($this->action === 'deleted') {
            return $this->old_values;
        }

        // For updates, show what changed
        $changes = [];
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue != $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }

    public function getModelRouteAttribute()
    {
        if (! $this->model_id) {
            return null;
        }

        // Map model types to their show routes
        $routeMap = [
            'App\\Models\\User' => 'users.show',
            'App\\Models\\Competition' => null, // No show route
            'App\\Models\\Quiz' => null, // No show route
            'App\\Models\\QuizQuestion' => null, // No show route
            'App\\Models\\Reward' => null, // No show route
            'App\\Models\\Order' => null, // No show route
            'App\\Models\\BonusPenalty' => 'bonus-penalties.show',
        ];

        $routeName = $routeMap[$this->model_type] ?? null;

        if ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) {
            try {
                return route($routeName, $this->model_id);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
