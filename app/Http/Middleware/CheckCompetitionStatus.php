<?php

namespace App\Http\Middleware;

use App\Repositories\CompetitionRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckCompetitionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Run competition status check once per day on first API request
        $cacheKey = 'competition_check_'.now()->toDateString();
        if (! Cache::has($cacheKey)) {
            $competitionRepository = app(CompetitionRepository::class);
            $competitionRepository->checkCompetition();
            Cache::put($cacheKey, true, now()->endOfDay());
        }

        return $next($request);
    }
}
