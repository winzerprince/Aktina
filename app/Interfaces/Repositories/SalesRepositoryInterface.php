<?php

namespace App\Interfaces\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface SalesRepositoryInterface
{
    /**
     * Get orders from production managers within date range
     */
    public function getProductionManagerOrders(Carbon $startDate, Carbon $endDate): Collection;

    /**
     * Get orders with flexible filtering options
     */
    public function getOrdersByDateRange(array $filters, Carbon $startDate, Carbon $endDate): Collection;

    /**
     * Get production managers user IDs for caching
     */
    public function getProductionManagerUserIds(): array;

    /**
     * Get all orders from production managers (no date filtering)
     */
    public function getAllProductionManagerOrders(): Collection;
}
