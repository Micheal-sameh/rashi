<?php

namespace App\Observers;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\User;
use App\Models\UserNotification;

class CompetitionObserver
{
    /**
     * Handle the Competition "created" event.
     */
    public function created(Competition $competition): void
    {
        //
    }

    /**
     * Handle the Competition "updated" event.
     */
    public function updated(Competition $competition): void
    {
        if ($competition->wasChanged('status') && $competition->status == CompetitionStatus::ACTIVE) {
            $this->sendCompetitionActivatedNotification($competition);
        }
    }

    /**
     * Handle the Competition "deleted" event.
     */
    public function deleted(Competition $competition): void
    {
        //
    }

    /**
     * Handle the Competition "restored" event.
     */
    public function restored(Competition $competition): void
    {
        //
    }

    /**
     * Handle the Competition "force deleted" event.
     */
    public function forceDeleted(Competition $competition): void
    {
        //
    }

    /**
     * Send notification when competition becomes active
     */
    private function sendCompetitionActivatedNotification(Competition $competition): void
    {
        // Get all users who are part of groups associated with this competition
        $users = User::whereHas('groups', function ($query) use ($competition) {
            $query->whereHas('competitions', function ($subQuery) use ($competition) {
                $subQuery->where('competitions.id', $competition->id);
            });
        })->get();
        dd($competition, $users);

        foreach ($users as $user) {
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Competition Activated',
                'message' => "The competition '{$competition->name}' is now active and available for participation.",
                'type' => 'success',
                'subject_type' => Competition::class,
                'subject_id' => $competition->id,
                'data' => [
                    'competition_id' => $competition->id,
                    'competition_name' => $competition->name,
                    'start_at' => $competition->start_at,
                    'end_at' => $competition->end_at,
                ],
            ]);
        }
    }
}
