<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        // Track when a model is created
        static::created(function ($model) {
            $model->auditLog('created');
        });

        // Track when a model is updated
        static::updated(function ($model) {
            $model->auditLog('updated');
        });

        // Track when a model is deleted
        static::deleted(function ($model) {
            $model->auditLog('deleted');
        });
    }

    protected function auditLog($action)
    {
        $user = Auth::user();

        // Determine old and new values based on action
        $oldValues = null;
        $newValues = null;

        switch ($action) {
            case 'created':
                $newValues = $this->getAuditableAttributes();
                break;

            case 'updated':
                $oldValues = $this->getOriginal();
                $newValues = $this->getAttributes();
                // Filter to only show changed attributes
                $changes = $this->getChanges();
                if (empty($changes)) {
                    return; // No actual changes, skip audit
                }
                $oldValues = array_intersect_key($oldValues, $changes);
                $newValues = $changes;
                break;

            case 'deleted':
                $oldValues = $this->getAuditableAttributes();
                break;
        }

        // Clean sensitive data
        $oldValues = $this->cleanSensitiveData($oldValues);
        $newValues = $this->cleanSensitiveData($newValues);

        AuditLog::create([
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected function getAuditableAttributes()
    {
        $attributes = $this->getAttributes();

        // Exclude timestamps if you don't want to track them
        if (property_exists($this, 'auditExclude')) {
            $attributes = array_diff_key($attributes, array_flip($this->auditExclude));
        }

        return $attributes;
    }

    protected function cleanSensitiveData($data)
    {
        if (! is_array($data)) {
            return $data;
        }

        // Define sensitive fields to exclude from audit
        $sensitiveFields = ['password', 'remember_token', 'api_token', 'secret'];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }

        return $data;
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }
}
