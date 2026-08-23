<?php

namespace App\Traits;

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
}
