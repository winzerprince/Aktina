<?php

namespace App\Interfaces\Services;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductionOrderServiceInterface
{
    /**
     * Get all production orders with optional filtering and pagination
     */
    public function getAllProductionOrders(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get production order by ID
     */
    public function getProductionOrderById(int $id): ?Order;

    /**
     * Get production orders by status
     */
    public function getOrdersByStatus(string $status, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get order statistics for production dashboard
     */
    public function getProductionOrderStatistics(Carbon $startDate, Carbon $endDate): array;

    /**
     * Update production order status
     */
    public function updateProductionOrderStatus(int $orderId, string $status, array $additionalData = []): bool;

    /**
     * Bulk update order statuses
     */
    public function bulkUpdateProductionOrders(array $orderIds, string $status): int;

    /**
     * Assign production order to employee(s)
     */
    public function assignProductionOrderToEmployees(int $orderId, array $employeeIds): bool;

    /**
     * Get valid next statuses for a production order
     */
    public function getValidNextStatuses(Order $order): array;

    /**
     * Schedule production for an order
     */
    public function scheduleProduction(int $orderId, array $productionDetails): bool;

    /**
     * Check resource availability for production
     */
    public function checkResourceAvailability(int $orderId): array;
}
