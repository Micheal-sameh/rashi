<?php

namespace App\Providers;

use App\Repositories\Contracts\ReportRepositoryContract;
use App\Repositories\Contracts\ReportScheduleRepositoryContract;
use App\Repositories\Contracts\ReportShareRepositoryContract;
use App\Repositories\ReportRepository;
use App\Repositories\ReportScheduleRepository;
use App\Repositories\ReportShareRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReportRepositoryContract::class, ReportRepository::class);
        $this->app->bind(ReportShareRepositoryContract::class, ReportShareRepository::class);
        $this->app->bind(ReportScheduleRepositoryContract::class, ReportScheduleRepository::class);
    }
}
