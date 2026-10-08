<?php

namespace App\Traits;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Mobile login plumbing shared by every way a user can log in to the app
 * (QR code via AuthController, Avarewase SSO via SsoAuthController).
 *
 * Expects the using controller to extend BaseController and to have
 * `$fcmTokenService` (FcmTokenService) and `$refreshTokenService`
 * (RefreshTokenService) properties.
 */
trait IssuesSanctumTokens
{
    protected function generateToken($user)
    {
        $tokenResult = $user->createToken(config('app.name'));
        $token = $tokenResult->plainTextToken;

        $expires = now()->addMinutes(config('sanctum.expiration') ?: 60);
        $tokenModel = $tokenResult->accessToken;
        $tokenModel->expires_at = $expires;
        $tokenModel->save();

        return $token;
    }

    /**
     * Everything after the user has been identified: register the device's
     * FCM token, mint the access + refresh token pair, and build the login
     * response. Callers load whatever relations UserResource should include
     * before calling this.
     */
    protected function completeMobileLogin(Request $request, User $user)
    {
        if ($request->has('fcm_token')) {
            $this->fcmTokenService->updateOrCreate([
                'user_id' => $user->id,
                'token' => $request->fcm_token,
                'device_type' => $request->device_type,
                'imei' => $request->imei,
            ]);
        }

        $token = $this->generateToken($user);
        $refreshToken = $this->refreshTokenService->createForUser(
            $user,
            $request->device_type ?? null,
            $request->imei ?? null
        );

        return $this->apiResponse([
            'token' => $token,
            'refresh_token' => $refreshToken,
            'user' => new UserResource($user),
        ], trans('messages.login successfuly'));
    }
}
