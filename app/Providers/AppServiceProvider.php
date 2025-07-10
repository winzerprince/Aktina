<?php

namespace App\Providers;

use App\Interfaces\Services\OrderServiceInterface;
use App\Services\OrderService;
use App\Interfaces\Repositories\OrderRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Interfaces\Services\ResourceOrderServiceInterface;
use App\Services\ResourceOrderService;
use App\Interfaces\Repositories\ResourceOrderRepositoryInterface;
use App\Repositories\ResourceOrderRepository;
use App\Interfaces\Services\CommunicationPermissionServiceInterface;
use App\Services\CommunicationPermissionService;
use App\Interfaces\Services\ConversationServiceInterface;
use App\Services\ConversationService;
use App\Interfaces\Services\MessageServiceInterface;
use App\Services\MessageService;
use App\Interfaces\Repositories\ConversationRepositoryInterface;
use App\Repositories\ConversationRepository;
use App\Interfaces\Repositories\MessageRepositoryInterface;
use App\Repositories\MessageRepository;
use App\Interfaces\Services\VendorSalesServiceInterface;
use App\Services\VendorSalesService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ResourceOrderRepositoryInterface::class, ResourceOrderRepository::class);
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);

        // Bind Service Interfaces
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(ResourceOrderServiceInterface::class, ResourceOrderService::class);
        $this->app->bind(CommunicationPermissionServiceInterface::class, CommunicationPermissionService::class);
        $this->app->bind(ConversationServiceInterface::class, ConversationService::class);
        $this->app->bind(MessageServiceInterface::class, MessageService::class);
        $this->app->bind(VendorSalesServiceInterface::class, VendorSalesService::class);

        // Register additional services
        $this->app->singleton(\App\Services\RetailerOrderService::class);
        $this->app->singleton(\App\Services\RetailerSalesService::class);
        $this->app->singleton(\App\Services\RetailerInventoryService::class);
        $this->app->singleton(\App\Services\SupplierService::class);
        $this->app->singleton(\App\Services\HRService::class);
        $this->app->singleton(\App\Services\RealtimeDataService::class);
        $this->app->singleton(\App\Services\ReportGeneratorService::class);
        $this->app->singleton(\App\Services\ReportSchedulerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
