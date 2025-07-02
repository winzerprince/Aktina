<?php

namespace App\Interfaces\Repositories;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface EnhancedOrderRepositoryInterface
{
    public function getOrdersByStatus(string $status): Collection;
    public function getOrdersByDateRange(Carbon $startDate, Carbon $endDate): Collection;
    public function getOrdersByUser(int $userId, string $role = null): Collection;
    public function getOrdersRequiringApproval(): Collection;
    public function getOrderAnalytics(Carbon $startDate, Carbon $endDate): array;
    public function updateOrderStatus(int $orderId, string $status, array $metadata = []): Order;
    public function getOrderWorkflow(int $orderId): array;
    public function getSupplyChainOrders(int $userId, string $role): array;
    public function getOrderValueByPeriod(string $period = 'daily'): array;
    public function getTopOrdersByValue(int $limit = 10): Collection;
    public function getOrdersBySupplier(int $supplierId): Collection;
    public function getOrdersByCustomer(int $customerId): Collection;
    public function getOrdersWithItems(): Collection;
    public function getOrderStatusHistory(int $orderId): array;
}
