<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SsoLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FcmTokenService;
use App\Traits\IssuesSanctumTokens;
use Avarewase\SsoClient\Contracts\ProvisionsAvarewaseUsers;
use Avarewase\SsoClient\Exceptions\AvarewaseConnectionException;
use Avarewase\SsoClient\Exceptions\AvarewaseTokenException;
use Avarewase\SsoClient\Facades\AvarewaseSso;

/**
 * Mobile login via the Avarewase SSO: the mobile app performs the OAuth2 +
 * PKCE authorization-code flow directly against the SSO server (it is its
 * own registered OAuth client there, distinct from this API), then hands
 * the resulting access token here. We never see the mobile app's client
 * secret or authorization code — we just validate the token by asking the
 * SSO for the identity it belongs to, then mint our own Sanctum token.
 */
class SsoAuthController extends BaseController
{
    use IssuesSanctumTokens;

    public function __construct(protected FcmTokenService $fcmTokenService) {}

    public function login(SsoLoginRequest $request, ProvisionsAvarewaseUsers $provisioner)
    {
        try {
            $userInfo = AvarewaseSso::userInfo($request->string('access_token'));
        } catch (AvarewaseConnectionException $e) {
            return $this->apiErrorResponse(trans('messages.sso_unavailable'), 503);
        } catch (AvarewaseTokenException $e) {
            return $this->apiErrorResponse(trans('auth.failed'), 401);
        }

        try {
            /** @var User $user */
            $user = $provisioner->resolve($userInfo);
        } catch (\RuntimeException $e) {
            return $this->apiErrorResponse($e->getMessage(), 422);
        }

        $user->load([
            'roles:id,name',
            'media',
            'stage:id,name,code',
            'groups' => function ($q) {
                $q->where('group_id', '!=', 1);
            },
        ]);

        if ($request->has('fcm_token')) {
            $this->fcmTokenService->updateOrCreate([
                'user_id' => $user->id,
                'token' => $request->fcm_token,
                'device_type' => $request->device_type,
                'imei' => $request->imei,
            ]);
        }

        $token = $this->generateToken($user);

        return $this->apiResponse([
            'token' => $token,
            'user' => new UserResource($user),
        ], trans('messages.login successfuly'));
    }
}
