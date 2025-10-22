<?php

namespace App\Events;

use App\Models\Competition;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompetitionStatusUpdated
{
    use Dispatchable, SerializesModels;

    public $competition;

    public $oldStatus;

    public $newStatus;

    public function __construct(Competition $competition, $oldStatus, $newStatus)
    {
        $this->competition = $competition;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
