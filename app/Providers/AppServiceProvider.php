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

        // Bind Service Interfaces
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(ResourceOrderServiceInterface::class, ResourceOrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
