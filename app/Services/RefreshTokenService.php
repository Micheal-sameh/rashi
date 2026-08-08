<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Repositories\RefreshTokenRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RefreshTokenService
{
    public function __construct(protected RefreshTokenRepository $repo) {}

    /**
     * Create a new refresh token for a user and return the plain text token
     */
    /**
     * Create a new refresh token for a user and return the plain text token.
     * Optionally attach device metadata so we can revoke per‑device later.
     */
    public function createForUser($user, ?string $deviceType = null, ?string $imei = null)
    {
        $plain = Str::random(64);
        $hashed = hash('sha256', $plain);
        $expires = now()->addMinutes(config('auth.refresh_token_expiration'));

        $data = [
            'user_id' => $user->id,
            'token' => $hashed,
            'expires_at' => $expires,
        ];

        if ($deviceType) {
            $data['device_type'] = $deviceType;
        }
        if ($imei) {
            $data['imei'] = $imei;
        }

        $this->repo->store($data);

        return $plain;
    }

    /**
     * Find a refresh token model by raw token string
     */
    public function findByPlain(string $plain)
    {
        $hashed = hash('sha256', $plain);

        return $this->repo->findByToken($hashed);
    }

    public function revoke(RefreshToken $token)
    {
        $token->revoke();
    }

    public function revokeAllForUser($userId)
    {
        $this->repo->deleteByUserId($userId);
    }

    /**
     * Revoke tokens belonging to a specific device (or any combination of the two
     * device identifiers).  If both arguments are null, this becomes a full revoke.
     */
    public function revokeForDevice($userId, ?string $deviceType = null, ?string $imei = null)
    {
        return $this->repo->deleteByUserAndDevice($userId, $deviceType, $imei);
    }

    /**
     * Rotate a given refresh token: revoke the old one and return a new plain-text string
     */
    public function rotate(RefreshToken $token)
    {
        $this->revoke($token);

        return $this->createForUser(
            $token->user,
            $token->device_type,
            $token->imei
        );
    }

    /**
     * Validate a plain-text refresh token and atomically rotate it.
     * Locks the row for the duration of the transaction so concurrent
     * refresh calls with the same token can't both succeed.
     *
     * @return array{0: string, 1: \App\Models\User} [newPlainRefreshToken, user]
     *
     * @throws \RuntimeException if the token is missing, revoked, or expired
     */
    public function rotateByPlain(string $plain): array
    {
        $hashed = hash('sha256', $plain);

        return DB::transaction(function () use ($hashed) {
            $token = RefreshToken::where('token', $hashed)->lockForUpdate()->first();

            if (! $token || $token->isRevoked() || $token->isExpired()) {
                throw new \RuntimeException('Invalid or expired refresh token');
            }

            $newPlain = $this->rotate($token);

            return [$newPlain, $token->user];
        });
    }
}
