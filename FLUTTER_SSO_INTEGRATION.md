# Flutter App: Login via Avarewase SSO

How the rashi Flutter app authenticates through the Avarewase SSO and gets a
rashi API token to use for everything else.

## Architecture

```
Flutter app ──(1. PKCE)──► auth.avarewase.com (SSO)
     │                            │
     │◄──(2. access_token)────────┘
     │
     ▼
POST rashiwrafi.avarewase.com/api/auth/sso/login   { access_token }
     │
     ▼
rashi backend validates the token against the SSO, provisions/updates
the User (+ stage + groups), returns its own Sanctum token
     │
     ▼
Flutter stores that Sanctum token, uses it as Bearer for every other
rashi API call from then on
```

The mobile app never talks to rashi with the SSO's token directly — it
trades the SSO token for a rashi-issued one, once, at login.

## 1. Register a mobile OAuth client on the SSO

Needs its own client, separate from the web admin one — public (no secret,
since a compiled app can't keep one), PKCE-only, with a custom URI scheme
redirect instead of an https one:

| Field | Value |
|---|---|
| Grant Type | Authorization Code (PKCE) |
| Public client | yes (no secret issued) |
| Redirect URI | e.g. `com.avarewase.rashi://oauth/callback` |
| Scopes | `openid profile email` |

Registered via `OAuthClientService::registerClient()` on the SSO admin (same
mechanism used for the web client) — the redirect URI must match exactly
whatever scheme the app is built with.

## 2. Flutter side: do the PKCE flow against the SSO

Use [`flutter_appauth`](https://pub.dev/packages/flutter_appauth) — it
handles PKCE generation, opens the system browser/Custom Tab, and catches
the redirect:

```dart
final appAuth = FlutterAppAuth();

final result = await appAuth.authorizeAndExchangeCode(
  AuthorizationTokenRequest(
    ssoClientId,                          // the public client_id from step 1
    'com.avarewase.rashi://oauth/callback',
    issuer: null,
    serviceConfiguration: AuthorizationServiceConfiguration(
      authorizationEndpoint: 'https://auth.avarewase.com/oauth/authorize',
      tokenEndpoint: 'https://auth.avarewase.com/oauth/token',
    ),
    scopes: ['openid', 'profile', 'email'],
  ),
);

final ssoAccessToken = result.accessToken;
```

This never touches rashi — it's purely the app talking to the SSO.
`flutter_appauth` does PKCE (`code_challenge`/`code_verifier`) for you
automatically for a public client.

## 3. Exchange the SSO token for a rashi token

```dart
final response = await http.post(
  Uri.parse('https://rashiwrafi.avarewase.com/api/auth/sso/login'),
  headers: {'Accept': 'application/json'},
  body: {
    'access_token': ssoAccessToken,
    'fcm_token': fcmToken,      // optional, same as the existing QR login
    'device_type': 'android',   // or 'ios'
  },
);
// { "data": { "token": "...", "user": {...} } }
```

That's `POST /api/auth/sso/login`
(`app/Http/Controllers/Api/SsoAuthController.php`) — it validates the SSO
token via `/api/userinfo`, runs it through `RashiAvarewaseUserProvisioner`
(matches/creates the user, syncs stage + groups), and returns a Sanctum
token in the same shape as the existing QR-code login response (`token` +
`user`).

## 4. Use the rashi token for everything else

```dart
headers: {'Authorization': 'Bearer $rashiToken'}
```

Same as the app already does after QR login — no change to any other
endpoint.

## Things to plan for

- **Expiry, no refresh**: `SsoAuthController::generateToken` sets the
  Sanctum token to expire in `sanctum.expiration` minutes (60 by default),
  and `/api/auth/refresh` is currently hard-disabled
  (`AuthController::refresh` just returns 403). So on a 401, the app's only
  option today is to silently redo the SSO flow (AppAuth caches the SSO
  refresh token, so a refresh doesn't need a new browser prompt) and call
  `/api/auth/sso/login` again. If a real rashi-side refresh token is
  wanted, the plumbing (`RefreshToken` model/service) already exists in the
  codebase but is commented out in `AuthController`.
- **Logout**: calling rashi's `POST /api/auth/logout` only revokes the
  rashi Sanctum token — it doesn't touch the SSO session. To also end the
  SSO side, call `appAuth.endSession(...)` against `auth.avarewase.com`.
- **Membership code requirement**: the provisioner throws a 422 if the SSO
  identity has no `membership_code` and no existing local match — same
  constraint as the web flow, since rashi keys everything off it.
