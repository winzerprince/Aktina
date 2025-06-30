<?php

namespace App\Interfaces\Repositories;

use App\Models\ResourceOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface ResourceOrderRepositoryInterface
{
    /**
     * Get all resource orders
     */
    public function getAllResourceOrders(): Collection;

    /**
     * Get resource order by ID
     */
    public function getResourceOrderById(int $id): ?ResourceOrder;

    /**
     * Create new resource order
     */
    public function createResourceOrder(array $orderDetails): ResourceOrder;

    /**
     * Update resource order status
     */
    public function updateResourceOrderStatus(int $orderId, string $status): bool;

    /**
     * Get resource orders by buyer (Aktina)
     */
    public function getResourceOrdersByBuyer(int $buyerId): Collection;

    /**
     * Get resource orders by seller (Supplier)
     */
    public function getResourceOrdersBySeller(int $sellerId): Collection;

    /**
     * Get resource orders by status
     */
    public function getResourceOrdersByStatus(string $status): Collection;

    /**
     * Check resource stock levels for order items
     */
    public function checkResourceStockLevels(array $items): array;

    /**
     * Get resource orders within date range
     */
    public function getResourceOrdersByDateRange(Carbon $startDate, Carbon $endDate, array $filters = []): Collection;

    /**
     * Update resource inventory after order completion
     */
    public function updateResourceInventory(int $orderId): bool;
}
