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
     * Complete an order
     */
    public function completeOrder(int $orderId): bool;

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
