<?php

namespace App\Interfaces\Repositories;

use App\Models\Order;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    /**
     * Get all orders
     */
    public function getAllOrders(): Collection;

    /**
     * Get order by ID
     */
    public function getOrderById(int $id): ?Order;

    /**
     * Create new order
     */
    public function createOrder(array $orderDetails): Order;

    /**
     * Update order status
     */
    public function updateOrderStatus(int $orderId, string $status, array $additionalData = []): bool;

    /**
     * Get orders by buyer
     */
    public function getOrdersByBuyer(int $buyerId): Collection;

    /**
     * Get orders by seller
     */
    public function getOrdersBySeller(int $sellerId): Collection;

    /**
     * Get orders by status
     */
    public function getOrdersByStatus(string $status): Collection;

    /**
     * Check product stock levels for order items
     */
    public function checkProductStockLevels(array $items): array;

    /**
     * Assign employees to order
     */
    public function assignEmployeesToOrder(int $orderId, array $employeeIds): bool;

    /**
     * Update product ownership when order is complete
     */
    public function updateProductOwnership(int $orderId): bool;

    /**
     * Get orders within date range
     */
    public function getOrdersByDateRange(Carbon $startDate, Carbon $endDate, array $filters = []): Collection;

    /**
     * Get production managers user IDs for caching
     */
    public function getProductionManagerUserIds(): array;

    /**
     * Get orders with a specific status created after a certain date
     */
    public function getRecentOrdersByStatus(string $status, Carbon $afterDate): Collection;

    /**
     * Get orders that can be fulfilled (based on available inventory)
     */
    public function getFulfillableOrders(): Collection;

    /**
     * Get orders that need attention (late, at risk, etc.)
     */
    public function getOrdersNeedingAttention(): Collection;

    /**
     * Get order status timeline (all status changes with timestamps)
     */
    public function getOrderStatusTimeline(int $orderId): array;

    /**
     * Search orders with various filters
     */
    public function searchOrders(array $filters = []): Collection;
}
