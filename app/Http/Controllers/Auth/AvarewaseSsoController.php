<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Avarewase\SsoClient\Client\AvarewaseClient;
use Avarewase\SsoClient\Contracts\ProvisionsAvarewaseUsers;
use Avarewase\SsoClient\Events\AvarewaseUserAuthenticated;
use Avarewase\SsoClient\Exceptions\AvarewaseConnectionException;
use Avarewase\SsoClient\Exceptions\AvarewaseStateMismatchException;
use Avarewase\SsoClient\Exceptions\AvarewaseTokenException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Custom web login flow for the SSO — kept separate from the package's own
 * routes/controller (config('avarewase-sso.routes.enabled') is false, see
 * AppServiceProvider) because rashi's web dashboard is admin-only: a
 * successfully authenticated Avarewase identity that doesn't hold the
 * "admin" role locally must be rejected here rather than logged in, which
 * the package's default controller has no hook for.
 */
class AvarewaseSsoController extends Controller
{
    protected const SESSION_STATE_KEY = 'avarewase_sso.state';

    protected const SESSION_VERIFIER_KEY = 'avarewase_sso.code_verifier';

    public function __construct(protected AvarewaseClient $client)
    {
    }

    public function redirect(Request $request): RedirectResponse
    {
        $state = $this->client->pkce()->state();
        $verifier = $this->client->pkce()->verifier();
        $challenge = $this->client->pkce()->challengeFor($verifier);

        $request->session()->put(self::SESSION_STATE_KEY, $state);
        $request->session()->put(self::SESSION_VERIFIER_KEY, $verifier);

        return redirect()->away($this->client->authorizationUrl($state, $challenge));
    }

    public function callback(Request $request, ProvisionsAvarewaseUsers $provisioner): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $expectedState = $request->session()->pull(self::SESSION_STATE_KEY);
        $verifier = $request->session()->pull(self::SESSION_VERIFIER_KEY);

        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state'))) {
            throw new AvarewaseStateMismatchException('The Avarewase login state did not match.');
        }

        try {
            $tokens = $this->client->exchangeCodeForTokens($request->string('code'), $verifier);
            $userInfo = $this->client->userInfo($tokens->accessToken);
        } catch (AvarewaseConnectionException $e) {
            Log::warning('Avarewase SSO unreachable during web login callback.', ['error' => $e->getMessage()]);

            return redirect()->route('loginPage')
                ->withErrors(['membership_code' => __('messages.sso_unavailable')]);
        } catch (AvarewaseTokenException $e) {
            Log::info('Avarewase SSO rejected the login callback.', ['error' => $e->getMessage()]);

            return redirect()->route('loginPage')
                ->withErrors(['membership_code' => __('auth.failed')]);
        }

        /** @var User $user */
        $user = $provisioner->resolve($userInfo);

        if (! $user->hasRole(['admin'])) {
            return redirect()->route('loginPage')
                ->withErrors(['membership_code' => __('messages.unauthorized')]);
        }

        Auth::guard(config('avarewase-sso.guard'))->login($user, remember: true);

        $request->session()->regenerate();

        AvarewaseUserAuthenticated::dispatch($user, $userInfo, $tokens);

        return redirect()->intended(route('competitions.index'))->with('success', 'Welcome back, Admin!');
    }
}
