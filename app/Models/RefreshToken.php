<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefreshToken extends Model
{
    protected $fillable = [
        'user_id',
        'device_type',
        'imei',
        'token',
        'expires_at',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope tokens by device identifiers (either may be null).
     */
    public function scopeForDevice($query, $deviceType = null, $imei = null)
    {
        if (! is_null($deviceType)) {
            $query->where('device_type', $deviceType);
        }
        if (! is_null($imei)) {
            $query->where('imei', $imei);
        }

        return $query;
    }

    public function isExpired(): bool
    {
        return $this->expires_at ? $this->expires_at->isPast() : false;
    }

    public function isRevoked(): bool
    {
        return ! is_null($this->revoked_at);
    }

    public function revoke(): void
    {
        $this->revoked_at = now();
        $this->save();
    }
}
