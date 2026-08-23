<?php

namespace App\Auth;

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
        ], fn ($value) => ! is_null($value));

        if ($user) {
            $user->forceFill($attributes)->save();

            return $user;
        }

        if (! $userInfo->membershipCode) {
            throw new RuntimeException(
                'Avarewase SSO login has no membership_code and no matching local account exists — cannot provision a new rashi user.'
            );
        }

        return User::query()->forceCreate($attributes + [
            'membership_code' => $userInfo->membershipCode,
            'password' => Str::random(40),
        ]);
    }
}
