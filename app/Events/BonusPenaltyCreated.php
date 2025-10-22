<?php

namespace App\Events;

use App\Models\BonusPenalty;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BonusPenaltyCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public BonusPenalty $bonusPenalty
    ) {}
}
