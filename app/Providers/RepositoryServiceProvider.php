<?php

namespace App\Providers;

use App\Interfaces\Repositories\SalesRepositoryInterface;
use App\Interfaces\Repositories\ApplicationRepositoryInterface;
use App\Interfaces\Services\SalesAnalyticsServiceInterface;
use App\Interfaces\Services\VerificationServiceInterface;
use App\Interfaces\Services\ApplicationServiceInterface;
use App\Repositories\SalesRepository;
use App\Repositories\ApplicationRepository;
use App\Services\SalesAnalyticsService;
use App\Services\VerificationService;
use App\Services\ApplicationService;
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

        $this->app->bind(
            ApplicationRepositoryInterface::class,
            ApplicationRepository::class
        );

        // Bind Service Interfaces to Implementations
        $this->app->bind(
            SalesAnalyticsServiceInterface::class,
            SalesAnalyticsService::class
        );

        $this->app->bind(
            VerificationServiceInterface::class,
            VerificationService::class
        );

        $this->app->bind(
            ApplicationServiceInterface::class,
            ApplicationService::class
        );

        // Bind additional services
        $this->app->singleton(
            \App\Services\FileValidationService::class
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
