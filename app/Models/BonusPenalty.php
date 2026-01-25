<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BonusPenalty extends Model
{
    use Auditable, HasFactory;

    protected $table = 'bonuses_penalties';

    protected $fillable = [
        'user_id',
        'type',
        'points',
        'reason',
        'created_by',
        'status',
        'approved_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function addRecord($data)
    {
        // Check if creator is admin
        $creator = User::find(Auth::id());
        $isAdmin = $creator && $creator->hasRole('admin');

        $bonusPenalty = BonusPenalty::create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'points' => $data['points'],
            'reason' => $data['reason'],
            'created_by' => Auth::id(),
            'status' => $isAdmin ? \App\Enums\BonusPenaltyStatus::APPLIED : \App\Enums\BonusPenaltyStatus::PENDING_APPROVAL,
            'approved_by' => $isAdmin ? Auth::id() : null,
        ]);

        // Only add to point history if status is applied (auto-approved by admin)
        if ($isAdmin) {
            PointHistory::addRecord($bonusPenalty);
        }

        return $bonusPenalty;
    }
}
