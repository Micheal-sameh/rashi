<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheAuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            $cacheKey = 'auth_user_'.$user->id;
            if (! Cache::has($cacheKey)) {
                Cache::put($cacheKey, $user->load(['groups']), now()->addMinutes(10));
            }
        }

        return $next($request);
    }
}
