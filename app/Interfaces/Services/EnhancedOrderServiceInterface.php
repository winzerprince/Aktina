<?php

namespace App\Interfaces\Services;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface EnhancedOrderServiceInterface
{
    public function createOrder(array $orderData): Order;
    public function updateOrderStatus(int $orderId, string $status): Order;
    public function approveOrder(int $orderId, int $approverId): Order;
    public function rejectOrder(int $orderId, int $approverId, string $reason = null): Order;
    public function getOrdersByUser(int $userId): Collection;
    public function getOrdersByStatus(string $status): Collection;
    public function getOrdersRequiringApproval(int $userId = null): Collection;
    public function getOrderAnalytics(Carbon $startDate = null, Carbon $endDate = null): array;
    public function calculateOrderValue(array $items): float;
    public function checkInventoryAvailability(array $items): array;
    public function getOrderWorkflow(int $orderId): array;
    public function assignOrderToWarehouse(int $orderId, int $warehouseId): bool;
    public function processOrderShipment(int $orderId, array $shipmentData): Order;
    public function getSupplyChainOrders(int $userId): array;
}
