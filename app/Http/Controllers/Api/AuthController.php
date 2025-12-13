<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Resources\UserResource;
use App\Services\FcmTokenService;
use App\Services\UserService;
use App\Traits\ArAuthentication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AuthController extends BaseController
{
    use ArAuthentication;

    public function __construct(
        protected UserService $userService,
        protected FcmTokenService $fcmTokenService,
    ) {}

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->ArQrlogin($request->qr_code);
            $user->load([
                'roles:id,name',
                'media',
                'groups' => function ($q) {
                    $q->where('group_id', '!=', 1);
                },
            ]);
            $token = $this->generateToken($user);

            return $this->apiResponse([
                'token' => $token,
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

        $token = $user->currentAccessToken();
        $token->update([
            'expired_at' => now(),
        ]);
        $token->delete();
        auth()->guard('web')->logout();

        return $this->apiResponse(message: 'logout successfuly');
    }

    protected function generateToken($user)
    {
        $token = $user->createToken(config('app.name'))->plainTextToken;

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
        if ($timeDifference > 5 || $timeDifference < -5) {
            throw new \Exception('This code is expired - must be within 5 minutes');
        }

        // Parse membership components
        $familyNumberLength = (int) substr($code, 10, 1);
        $NR = substr($code, -$familyNumberLength);
        $membershipPart = substr($code, 13, -$familyNumberLength);
        $membership_code = "E1C1F{$membershipPart}NR{$NR}";
        $name = explode('|', $qr_code)[1];
        $group = explode('|', $qr_code)[2] ?? '';

        return compact('membership_code', 'name', 'group');
    }
}
