<?php

namespace App\Interfaces\Services;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface OrderServiceInterface
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
     * Process a new order
     */
    public function processNewOrder(array $orderData): Order;

    /**
     * Accept an order
     */
    public function acceptOrder(int $orderId): bool;

    /**
     * Reject an order
     */
    public function rejectOrder(int $orderId, string $reason = null): bool;

    /**
     * Cancel an order
     */
    public function cancelOrder(int $orderId): bool;

    /**
     * Process an order (move to processing state)
     */
    public function processOrder(int $orderId): bool;

    /**
     * Fulfill an order (prepare for shipping)
     */
    public function fulfillOrder(int $orderId): bool;

    /**
     * Partially fulfill an order
     */
    public function partiallyFulfillOrder(int $orderId, array $fulfilledItems): bool;

    /**
     * Ship an order
     */
    public function shipOrder(int $orderId, array $shippingDetails = []): bool;

    /**
     * Mark an order as delivered
     */
    public function deliverOrder(int $orderId): bool;

    /**
     * Complete an order
     */
    public function completeOrder(int $orderId): bool;

    /**
     * Process a return for an order
     */
    public function returnOrder(int $orderId, string $reason = null): bool;

    /**
     * Check stock availability for order
     */
    public function checkStockAvailability(array $items): array;

    /**
     * Select available employees for order fulfillment
     */
    public function selectAvailableEmployees(int $count = 4): array;

    /**
     * Get orders by user (buyer or seller)
     */
    public function getOrdersByUser(User $user): Collection;

    /**
     * Get buyer's orders
     */
    public function getBuyerOrders(User $user): Collection;

    /**
     * Get seller's orders
     */
    public function getSellerOrders(User $user): Collection;

    /**
     * Get orders by date range with optional filters
     */
    public function getOrdersByDateRange(Carbon $startDate, Carbon $endDate, array $filters = []): Collection;

    /**
     * Get order statistics
     */
    public function getOrderStatistics(Carbon $startDate, Carbon $endDate): array;

    /**
     * Generate notifications for new orders
     */
    public function sendOrderNotification(Order $order, string $type): void;
}
