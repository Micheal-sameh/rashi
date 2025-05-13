<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // $lang = $request->route('lang', session('lang', 'en'));
        $lang = $request->header('Accept-Language') ?? 'en';
        $languageCode = explode(',', $lang)[0];
        App::setLocale($languageCode);
        session(['lang' => $lang]);
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
            // For web requests, use route parameter or session fallback
            $locale = $request->route('lang', session('lang', $defaultLocale));
        }

        // Apply locale and store in session
        App::setLocale($locale);
        session(['lang' => $locale]);

        return $next($request);
    }
}
