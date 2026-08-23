<?php

namespace App\Auth;

use App\Models\Group;
use App\Models\Stage;
use App\Models\User;
use Avarewase\SsoClient\Contracts\ProvisionsAvarewaseUsers;
use Avarewase\SsoClient\DataObjects\AvarewaseUserInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Matches an Avarewase SSO identity to rashi's own User model, keyed on
 * `membership_code` (rashi's actual business identifier) rather than the
 * package's default email-based matching, since many members have no email
 * on file. Falls back to `avarewase_sub`/`email` for users that already
 * exist by those keys (e.g. legacy local accounts), and backfills
 * `avarewase_sub` on them so future logins match directly.
 *
 * Also keeps the user's stage and group memberships in sync with the SSO on
 * every login — the SSO is the source of truth for both, since rashi has no
 * UI of its own for assigning either to a user.
 */
class RashiAvarewaseUserProvisioner implements ProvisionsAvarewaseUsers
{
    public function resolve(AvarewaseUserInfo $userInfo): Authenticatable
    {
        $user = User::query()->where('avarewase_sub', $userInfo->sub)->first();

        if (! $user && $userInfo->membershipCode) {
            $user = User::query()->where('membership_code', $userInfo->membershipCode)->first();
        }

        if (! $user && $userInfo->email) {
            $user = User::query()->where('email', $userInfo->email)->first();
        }

        $attributes = array_filter([
            'name' => $userInfo->name,
            'email' => $userInfo->email,
            'avarewase_sub' => $userInfo->sub,
            'avarewase_avatar' => $userInfo->picture,
            'email_verified_at' => $userInfo->emailVerified ? now() : null,
            'stage_id' => $this->resolveStageId($userInfo->raw['stage'] ?? null),
        ], fn ($value) => ! is_null($value));

        if ($user) {
            $user->forceFill($attributes)->save();
        } else {
            if (! $userInfo->membershipCode) {
                throw new RuntimeException(
                    'Avarewase SSO login has no membership_code and no matching local account exists — cannot provision a new rashi user.'
                );
            }

            $user = User::query()->forceCreate($attributes + [
                'membership_code' => $userInfo->membershipCode,
                'password' => Str::random(40),
            ]);
        }

        $this->syncGroups($user, $userInfo->raw['groups'] ?? null);

        return $user;
    }

    /**
     * Find-or-create the local Stage matching the SSO's stage (keyed by
     * `code`, its stable identifier), keeping the name in sync in case it
     * was renamed on the SSO side. Returns null if the SSO sent no stage.
     */
    protected function resolveStageId(?array $ssoStage): ?int
    {
        if (! $ssoStage || empty($ssoStage['code'])) {
            return null;
        }

        $stage = Stage::query()->updateOrCreate(
            ['code' => $ssoStage['code']],
            ['name' => $ssoStage['name'] ?? $ssoStage['code']],
        );

        return $stage->id;
    }

    /**
     * Find-or-create local Groups matching the SSO's group names and sync
     * them onto the user, replacing whatever group set they previously had
     * here — the SSO's group list for a user is authoritative. The SSO only
     * sends a `name` (no `abbreviation`), unlike rashi's own group-picker
     * UI, so matching is by name. Group id 1 ("general") is always kept,
     * mirroring the baseline every user gets via the mobile QR-code login
     * (see UserRepository::assignGroups()).
     */
    protected function syncGroups(User $user, ?array $ssoGroups): void
    {
        if ($ssoGroups === null) {
            return;
        }

        $groupIds = collect($ssoGroups)
            ->pluck('name')
            ->filter()
            ->map(fn (string $name) => Group::query()->firstOrCreate(['name' => $name])->id)
            ->all();

        $user->groups()->sync(array_unique(array_merge([1], $groupIds)));
    }
}
