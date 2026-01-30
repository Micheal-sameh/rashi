<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransfer extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'points',
        'family_code',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Sender relationship
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Receiver relationship
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Creator relationship (admin who created the transfer)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the family code from membership code
     */
    public static function extractFamilyCode(string $membershipCode): ?string
    {
        // Extract E1C1Fxxx pattern from membership_code
        if (preg_match('/E\d+C\d+F\d+/', $membershipCode, $matches)) {
            return $matches[0];
        }

        return null;
    }
}
