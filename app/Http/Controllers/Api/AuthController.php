<?php

namespace App\Http\Controllers\Api;

use App\DTOs\UserLoginDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class AuthController extends BaseController
{
    public function __construct(
        protected UserService $userService,
    ) {}

    public function login(LoginRequest $request)
    {
        $input = new UserLoginDTO(...$request->only(
            'membership_code', 'password', 'name', 'email', 'phone',
        ));
        $user = $this->userService->updateOrcreate($input);
        $token = $this->generateToken($user);

        return $this->apiResponse([
            'token' => $token,
            'user' => new UserResource($user),
        ], trans('messages.login successfuly'));
    }

    public function logout()
    {
        $user = auth()->user();
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
}
