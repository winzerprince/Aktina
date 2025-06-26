<?php

namespace App\Providers;

use App\Interfaces\Repositories\SalesRepositoryInterface;
use App\Interfaces\Services\SalesAnalyticsServiceInterface;
use App\Repositories\SalesRepository;
use App\Services\SalesAnalyticsService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(
            SalesRepositoryInterface::class,
            SalesRepository::class
        );

        // Bind Service Interfaces to Implementations
        $this->app->bind(
            SalesAnalyticsServiceInterface::class,
            SalesAnalyticsService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
