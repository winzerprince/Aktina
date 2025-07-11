<?php

namespace App\Providers;

use App\Interfaces\Repositories\SalesRepositoryInterface;
use App\Interfaces\Repositories\ApplicationRepositoryInterface;
use App\Interfaces\Repositories\MessageRepositoryInterface;
use App\Interfaces\Repositories\ConversationRepositoryInterface;
use App\Interfaces\Repositories\WarehouseRepositoryInterface;
use App\Interfaces\Repositories\InventoryRepositoryInterface;
use App\Interfaces\Repositories\AlertRepositoryInterface;
use App\Interfaces\Repositories\AnalyticsRepositoryInterface;
use App\Interfaces\Repositories\MetricsRepositoryInterface;
use App\Interfaces\Repositories\ReportRepositoryInterface;
use App\Interfaces\Repositories\EnhancedOrderRepositoryInterface;
use App\Interfaces\Services\SalesAnalyticsServiceInterface;
use App\Interfaces\Services\VerificationServiceInterface;
use App\Interfaces\Services\ApplicationServiceInterface;
use App\Interfaces\Services\VendorApplicationServiceInterface;
use App\Interfaces\Services\MessageServiceInterface;
use App\Interfaces\Services\ConversationServiceInterface;
use App\Interfaces\Services\WarehouseServiceInterface;
use App\Interfaces\Services\InventoryServiceInterface;
use App\Interfaces\Services\AlertServiceInterface;
use App\Interfaces\Services\AnalyticsServiceInterface;
use App\Interfaces\Services\MetricsServiceInterface;
use App\Interfaces\Services\ReportServiceInterface;
use App\Interfaces\Services\EnhancedOrderServiceInterface;
use App\Interfaces\Services\AlertEnhancementServiceInterface;
use App\Interfaces\Services\UserManagementServiceInterface;
use App\Repositories\SalesRepository;
use App\Repositories\ApplicationRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ConversationRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\InventoryRepository;
use App\Repositories\AlertRepository;
use App\Repositories\AnalyticsRepository;
use App\Repositories\MetricsRepository;
use App\Repositories\ReportRepository;
use App\Repositories\EnhancedOrderRepository;
use App\Services\SalesAnalyticsService;
use App\Services\VerificationService;
use App\Services\ApplicationService;
use App\Services\VendorApplicationService;
use App\Services\MessageService;
use App\Services\ConversationService;
use App\Services\WarehouseService;
use App\Services\InventoryService;
use App\Services\AlertService;
use App\Services\AlertEnhancementService;
use App\Services\AnalyticsService;
use App\Services\MetricsService;
use App\Services\ReportService;
use App\Services\EnhancedOrderService;
use App\Services\UserManagementService;
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

        $this->app->bind(
            MessageRepositoryInterface::class,
            MessageRepository::class
        );

        $this->app->bind(
            ConversationRepositoryInterface::class,
            ConversationRepository::class
        );

        $this->app->bind(
            WarehouseRepositoryInterface::class,
            WarehouseRepository::class
        );

        $this->app->bind(
            InventoryRepositoryInterface::class,
            InventoryRepository::class
        );

        $this->app->bind(
            AlertRepositoryInterface::class,
            AlertRepository::class
        );

        $this->app->bind(
            AnalyticsRepositoryInterface::class,
            AnalyticsRepository::class
        );

        $this->app->bind(
            MetricsRepositoryInterface::class,
            MetricsRepository::class
        );

        $this->app->bind(
            ReportRepositoryInterface::class,
            ReportRepository::class
        );

        $this->app->bind(
            EnhancedOrderRepositoryInterface::class,
            EnhancedOrderRepository::class
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

        $this->app->bind(
            VendorApplicationServiceInterface::class,
            VendorApplicationService::class
        );

        $this->app->bind(
            UserManagementServiceInterface::class,
            UserManagementService::class
        );

        $this->app->bind(
            MessageServiceInterface::class,
            MessageService::class
        );

        $this->app->bind(
            ConversationServiceInterface::class,
            ConversationService::class
        );

        $this->app->bind(
            WarehouseServiceInterface::class,
            WarehouseService::class
        );

        $this->app->bind(
            InventoryServiceInterface::class,
            InventoryService::class
        );

        $this->app->bind(
            AlertServiceInterface::class,
            AlertService::class
        );

        $this->app->bind(
            AnalyticsServiceInterface::class,
            AnalyticsService::class
        );

        $this->app->bind(
            MetricsServiceInterface::class,
            MetricsService::class
        );

        $this->app->bind(
            ReportServiceInterface::class,
            ReportService::class
        );

        $this->app->bind(
            EnhancedOrderServiceInterface::class,
            EnhancedOrderService::class
        );

        $this->app->bind(
            AlertEnhancementServiceInterface::class,
            AlertEnhancementService::class
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
