<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function view(User $user, Report $report): bool
    {
        return $report->created_by === $user->id
            || $report->sharedWith()->where('users.id', $user->id)->exists();
    }

    public function update(User $user, Report $report): bool
    {
        return $report->created_by === $user->id;
    }

    public function delete(User $user, Report $report): bool
    {
        return $report->created_by === $user->id;
    }

    public function share(User $user, Report $report): bool
    {
        return $report->created_by === $user->id;
    }

    public function schedule(User $user, Report $report): bool
    {
        return $report->created_by === $user->id;
    }
}
