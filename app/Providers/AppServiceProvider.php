<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.sideBar', function ($view) {
            $currentRoute = request()->route();
            $view->with('activeRoutes', [
                'users' => $currentRoute && $currentRoute->named('users.*'),
                'leaderboard' => $currentRoute && $currentRoute->named('users.leaderboard'),
                'competitions' => $currentRoute && $currentRoute->named('competitions.*'),
                'quizzes' => $currentRoute && $currentRoute->named('quizzes.*'),
                'questions' => $currentRoute && $currentRoute->named('questions.*'),
                'settings' => $currentRoute && $currentRoute->named('settings.*'),
                'groups' => $currentRoute && $currentRoute->named('groups.*'),
                'bonus-penalties' => $currentRoute && $currentRoute->named('bonus-penalties.*'),
                'rewards' => $currentRoute && $currentRoute->named('rewards.*'),
                'orders' => $currentRoute && $currentRoute->named('orders.*'),
                'notifications' => $currentRoute && $currentRoute->named('notifications.*'),
            ]);
        });
    }
}
