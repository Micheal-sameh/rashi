<?php

namespace App\Http\Controllers\Auth;

use App\Services\RefreshTokenService;
use Avarewase\SsoClient\Contracts\ProvisionsAvarewaseUsers;
use Avarewase\SsoClient\Http\Controllers\AvarewaseLogoutWebhookController;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * The package's back-channel webhook, extended to also revoke rashi's own
 * mobile refresh tokens on `user.access_revoked`. The package only deletes
 * Sanctum tokens — but a surviving refresh token can mint a fresh Sanctum
 * token via POST /api/refresh, so a revoked user would otherwise stay
 * logged in on mobile until the refresh token expired.
 *
 * Swapped in for the package's controller via the container binding in
 * AppServiceProvider; the package's route definition is unchanged.
 */
class RashiAvarewaseLogoutWebhookController extends AvarewaseLogoutWebhookController
{
    public function __construct(
        ProvisionsAvarewaseUsers $provisioner,
        protected RefreshTokenService $refreshTokenService,
    ) {
        parent::__construct($provisioner);
    }

    protected function revokeSanctumTokens(Authenticatable $user): void
    {
        $this->refreshTokenService->revokeAllForUser($user->getAuthIdentifier());

        parent::revokeSanctumTokens($user);
    }
}
