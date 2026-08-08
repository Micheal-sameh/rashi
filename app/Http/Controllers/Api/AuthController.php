<?php

namespace App\Http\Controllers\Api;

use App\DTOs\UserLoginDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Resources\UserResource;
use App\Services\FcmTokenService;
use App\Services\QrCodeCredentialsService;
use App\Services\RefreshTokenService;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;

class AuthController extends BaseController
{
    public function __construct(
        protected UserService $userService,
        protected FcmTokenService $fcmTokenService,
        protected RefreshTokenService $refreshTokenService,
        protected QrCodeCredentialsService $qrCodeCredentialsService,
    ) {}

    public function login(LoginRequest $request)
    {
        try {
            $credentials = collect($this->qrCodeCredentialsService->decode($request->qr_code));

            $input = new UserLoginDTO(...$credentials->only(
                'membership_code',
                'name',
                'groups',
                // 'password',
                // 'email',
            ));

            $user = $this->userService->updateOrcreate($input);
            $user->load([
                'roles:id,name',
                'media',
                'groups' => function ($q) {
                    $q->where('group_id', '!=', 1);
                },
            ]);

            $this->updateOrCreateFcmToken($request, $user);

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

        } catch (\Exception $e) {
            return $this->apiErrorResponse($e->getMessage(), 400);
        }
    }

    public function logout(LogoutRequest $request)
    {
        $user = Cache::get('auth_user_'.auth()->id()) ?? auth()->user();

        // Delete specific FCM token if provided
        if ($request->has('fcm_token')) {
            $this->fcmTokenService->deleteByToken($request->fcm_token);
        }

        // revoke access token
        $token = $user->currentAccessToken();
        $token->delete();

        // revoke refresh tokens for this device only (if identifiers present)
        if ($request->has('device_type') || $request->has('imei')) {
            $this->refreshTokenService->revokeForDevice(
                $user->id,
                $request->device_type,
                $request->imei
            );
        } else {
            // fallback: revoke everything
            $this->refreshTokenService->revokeAllForUser($user->id);
        }

        auth()->guard('web')->logout();

        return $this->apiResponse(message: 'logout successfuly');
    }

    protected function generateToken($user)
    {
        $tokenResult = $user->createToken(config('app.name'));
        $token = $tokenResult->plainTextToken;

        // set short expiration (use sanctum expiration configuration if available)
        $expires = now()->addMinutes(config('sanctum.expiration') ?: 60);
        $tokenModel = $tokenResult->accessToken;
        $tokenModel->expires_at = $expires;
        $tokenModel->save();

        return $token;
    }

    private function updateOrCreateFcmToken($request, $user)
    {
        if ($request->has('fcm_token')) {
            $data = [
                'user_id' => $user->id,
                'token' => $request->fcm_token,
                'device_type' => $request->device_type,
                'imei' => $request->imei,
            ];

            $this->fcmTokenService->updateOrCreate($data);
        }
    }

    /**
     * Exchange an unexpired refresh token for a new access token.
     */
    public function refresh(RefreshTokenRequest $request)
    {
        try {
            [$refreshToken, $user] = $this->refreshTokenService->rotateByPlain($request->refresh_token);
            $token = $this->generateToken($user);

            return $this->apiResponse([
                'token' => $token,
                'refresh_token' => $refreshToken,
            ], 'token refreshed successfully');
        } catch (\RuntimeException $e) {
            return $this->apiErrorResponse($e->getMessage(), 401);
        }
    }
}
