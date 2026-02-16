<?php

namespace App\Http\Controllers\Api;

use App\DTOs\UserLoginDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Resources\UserResource;
use App\Services\FcmTokenService;
use App\Services\RefreshTokenService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AuthController extends BaseController
{
    public function __construct(
        protected UserService $userService,
        protected FcmTokenService $fcmTokenService,
        protected RefreshTokenService $refreshTokenService,
    ) {}

    public function login(LoginRequest $request)
    {
        try {
            $credentials = collect($this->getCredentials($request->qr_code));

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
            // create a refresh token pair, include device metadata if provided
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
        $token->update([
            'expired_at' => now(),
        ]);
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

    private function getCredentials($qr_code)
    {
        $code = explode('|', $qr_code)[0];

        // Parse datetime components
        $minutes = (int) substr($code, 0, 2);
        $hours = (int) substr($code, 2, 2);
        $day = (int) substr($code, 4, 2);
        $month = (int) substr($code, 6, 2);
        $year = 2000 + (int) substr($code, 8, 2);
        $dateTime = Carbon::create($year, $month, $day, $hours, $minutes);
        // Validate datetime is within 5 minutes of current time
        $timeDifference = now()->diffInMinutes($dateTime, false); // false = signed difference
        // if ($timeDifference > 5 || $timeDifference < -5) {
        //     throw new \Exception('This code is expired - must be within 5 minutes');
        // }

        // Parse membership components
        $familyNumberLength = (int) substr($code, 10, 1);
        $NR = substr($code, -$familyNumberLength);
        $membershipPart = substr($code, 13, -$familyNumberLength);
        $membership_code = "E1C1F{$membershipPart}NR{$NR}";
        $name = explode('|', $qr_code)[1];
        $group = explode('|', $qr_code)[2] ?? '';
        $groups = [];
        if ($group) {
            $groups = explode(',', $group);
        }

        return compact('membership_code', 'name', 'groups');
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
        $refresh = $this->refreshTokenService->findByPlain($request->refresh_token);

        if (! $refresh || $refresh->isExpired() || $refresh->isRevoked()) {
            return $this->apiErrorResponse('invalid refresh token', 401);
        }

        $user = $refresh->user;
        $newAccessToken = $this->generateToken($user);
        $newRefresh = $this->refreshTokenService->rotate($refresh);

        return $this->apiResponse([
            'token' => $newAccessToken,
            'refresh_token' => $newRefresh,
            'user' => new UserResource($user),
        ], trans('messages.token_refreshed'));
    }
}
