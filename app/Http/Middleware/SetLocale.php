<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Default locale
        $defaultLocale = 'ar';
        $locale = $defaultLocale;

        // For API requests, use Accept-Language header
        if ($request->is('api/*')) {
            $acceptLang = $request->header('Accept-Language');
            if ($acceptLang) {
                $locale = substr(explode(',', $acceptLang)[0], 0, 2); // Normalize
            }
        } else {
            // For web requests, use route parameter or session fallback.
            // Request::route() ignores its $default argument whenever no route has
            // been resolved yet (e.g. during global middleware, or on a 404), so the
            // fallback must be applied with ?? instead of passed as the 2nd argument.
            $locale = $request->route('lang') ?? session('lang', $defaultLocale);
        }

        // Apply locale and store in session
        App::setLocale($locale);
        session(['lang' => $locale]);

        return $next($request);
    }
}
