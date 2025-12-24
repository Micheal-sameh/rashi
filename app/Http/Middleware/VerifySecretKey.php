<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifySecretKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $hashedSecretKey = $request['secret_key'];

        if (! $hashedSecretKey) {
            abort(401, 'Secret key is required');
        }

        $decodedSecretKey = base64_decode($hashedSecretKey);

        if ($decodedSecretKey !== env('AR_SECRET_KEY')) {
            abort(401, 'Invalid secret key');
        }

        return $next($request);
    }
}
