<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        if (! Hash::check(env('AR_SECRET_KEY'), $hashedSecretKey)) {
            abort(401, 'Invalid secret key');
        }

        return $next($request);
    }
}
