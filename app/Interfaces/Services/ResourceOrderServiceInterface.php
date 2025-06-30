<?php

namespace App\Interfaces\Services;

use App\Models\ResourceOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface ResourceOrderServiceInterface
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
     * Process a new resource order
     */
    public function processNewResourceOrder(array $orderData): ResourceOrder;

    /**
     * Accept a resource order
     */
    public function acceptResourceOrder(int $orderId): bool;

    /**
     * Complete a resource order
     */
    public function completeResourceOrder(int $orderId): bool;

    /**
     * Check stock availability for resource order
     */
    public function checkResourceStockAvailability(array $items): array;

    /**
     * Get resource orders by user (buyer or seller)
     */
    public function getResourceOrdersByUser(User $user): Collection;

    /**
     * Get Aktina's resource orders (as buyer)
     */
    public function getAktinaResourceOrders(User $user): Collection;

    /**
     * Get supplier's resource orders (as seller)
     */
    public function getSupplierResourceOrders(User $user): Collection;

    /**
     * Get resource orders by date range with optional filters
     */
    public function getResourceOrdersByDateRange(Carbon $startDate, Carbon $endDate, array $filters = []): Collection;

    /**
     * Get resource order statistics
     */
    public function getResourceOrderStatistics(Carbon $startDate, Carbon $endDate): array;

    /**
     * Generate notifications for new resource orders
     */
    public function sendResourceOrderNotification(ResourceOrder $order, string $type): void;
}
