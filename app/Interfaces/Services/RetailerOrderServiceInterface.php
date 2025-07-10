<?php

namespace App\Interfaces\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RetailerOrderServiceInterface
{
    /**
     * Get order statistics for a retailer
     */
    public function getOrderStats(User $retailer): array;

    /**
     * Get recent orders for a retailer
     */
    public function getRecentOrders(User $retailer, int $limit = 10): Collection;

    /**
     * Get orders for a retailer filtered by status
     */
    public function getOrdersByStatus(User $retailer, ?string $status = null): LengthAwarePaginator;

    /**
     * Get order trends over time for a retailer
     */
    public function getOrderTrends(User $retailer, int $days = 30): array;

    /**
     * Get order status distribution for a retailer
     */
    public function getOrderStatusDistribution(User $retailer): array;

    /**
     * Get top ordered products for a retailer
     */
    public function getTopOrderedProducts(User $retailer, int $limit = 10);

    /**
     * Get performance metrics for a retailer
     */
    public function getOrderPerformanceMetrics(User $retailer): array;

    /**
     * Validate order data before creation
     */
    public function validateOrderData(array $orderData): array;

    /**
     * Create a new order for a retailer
     */
    public function createOrder(User $retailer, array $orderData): Order;

    /**
     * Calculate order total and verify pricing
     */
    public function calculateOrderTotal(array $items): float;

    /**
     * Cancel an existing order if it's in a cancellable state
     */
    public function cancelOrder(User $retailer, int $orderId): bool;
}
