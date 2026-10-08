<?php

namespace App\Auth;

/**
 * Checks which OAuth client an Avarewase SSO access token was issued to.
 *
 * The SSO's /api/userinfo endpoint answers for any valid token, no matter
 * which client it was minted for — so without this check, an access token
 * a user granted to some *other* app on the same SSO could be replayed here
 * to log in as them.
 *
 * Only call this AFTER the SSO has accepted the token (userinfo succeeded):
 * the token is a signed JWT, and we decode its payload without verifying
 * the signature ourselves, relying on the SSO having already done so. A
 * tampered `aud` would have broken the signature and been rejected there.
 */
class AvarewaseTokenAudience
{
    public function isAllowed(string $accessToken): bool
    {
        $allowed = array_filter((array) config('avarewase-sso.allowed_audiences', []));

        if ($allowed === []) {
            return false;
        }

        $audiences = $this->audiences($accessToken);

        return $audiences !== [] && array_intersect($audiences, $allowed) !== [];
    }

    /**
     * @return string[] the token's `aud` claim, or [] if it can't be read
     */
    protected function audiences(string $accessToken): array
    {
        $parts = explode('.', $accessToken);

        if (count($parts) !== 3) {
            return [];
        }

        $json = base64_decode(strtr($parts[1], '-_', '+/'), true);
        $payload = $json === false ? null : json_decode($json, true);

        if (! is_array($payload) || ! isset($payload['aud'])) {
            return [];
        }

        return array_values(array_filter((array) $payload['aud'], 'is_string'));
    }
}
