<?php

namespace App\Services;

use App\Interfaces\Services\EnhancedOrderServiceInterface;
use App\Interfaces\Repositories\EnhancedOrderRepositoryInterface;
use App\Interfaces\Services\InventoryServiceInterface;
use App\Interfaces\Services\WarehouseServiceInterface;
use App\Models\Order;
use App\Models\User;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnhancedOrderService implements EnhancedOrderServiceInterface
{
    public function __construct(
        private EnhancedOrderRepositoryInterface $orderRepository,
        private InventoryServiceInterface $inventoryService,
        private WarehouseServiceInterface $warehouseService
    ) {}

    public function createOrder(array $orderData): Order
    {
        DB::beginTransaction();

        try {
            // Calculate order value
            $totalValue = $this->calculateOrderValue($orderData['items']);

            // Check inventory availability
            $availability = $this->checkInventoryAvailability($orderData['items']);
            if (!$availability['available']) {
                throw new \Exception('Insufficient inventory for order items: ' . implode(', ', $availability['unavailable_items']));
            }

            // Assign optimal warehouse
            $warehouseId = $this->warehouseService->assignOptimalWarehouse($orderData['items']);

            // Create the order through repository
            $order = $this->orderRepository->create([
                'buyer_id' => $orderData['buyer_id'],
                'seller_id' => $orderData['seller_id'],
                'items' => $orderData['items'],
                'price' => $totalValue,
                'status' => Order::STATUS_PENDING,
                'notes' => $orderData['notes'] ?? null,
                'delivery_address' => $orderData['delivery_address'] ?? null,
                'expected_delivery_date' => $orderData['expected_delivery_date'] ?? null
            ]);

            // Reserve inventory for the order
            foreach ($orderData['items'] as $item) {
                $this->inventoryService->reserveInventory(
                    $item['resource_id'],
                    $item['quantity'],
                    "Order #{$order->id}"
                );
            }

            DB::commit();

            // Clear relevant caches
            $this->clearOrderCaches();

            Log::info("Order created successfully", ['order_id' => $order->id, 'total_value' => $totalValue]);

            return $order->load(['buyer', 'seller']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Order creation failed", ['error' => $e->getMessage(), 'order_data' => $orderData]);
            throw $e;
        }
    }

    public function updateOrderStatus(int $orderId, string $status): Order
    {
        $validStatuses = [Order::STATUS_PENDING, Order::STATUS_ACCEPTED, Order::STATUS_COMPLETE];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid order status: {$status}");
        }

        $order = $this->orderRepository->updateOrderStatus($orderId, $status);

        // Handle status-specific logic
        if ($status === Order::STATUS_COMPLETE) {
            $this->processOrderCompletion($order);
        }

        $this->clearOrderCaches();

        Log::info("Order status updated", ['order_id' => $orderId, 'new_status' => $status]);

        return $order;
    }

    public function approveOrder(int $orderId, int $approverId): Order
    {
        $order = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_ACCEPTED, [
            'approver_id' => $approverId,
            'approved_at' => now()
        ]);

        $this->clearOrderCaches();

        Log::info("Order approved", ['order_id' => $orderId, 'approver_id' => $approverId]);

        return $order;
    }

    public function rejectOrder(int $orderId, int $approverId, string $reason = null): Order
    {
        return $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_REJECTED, [
            'approver_id' => $approverId,
            'notes' => $reason ? "Rejected: " . $reason : 'Rejected'
        ]);
    }

    public function cancelOrder(int $orderId, string $reason = null): Order
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            throw new \InvalidArgumentException('Order not found');
        }

        // Only allow cancellation of pending orders
        if ($order->status !== Order::STATUS_PENDING) {
            throw new \Exception('Only pending orders can be cancelled');
        }

        return $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_CANCELLED, [
            'notes' => $reason ? "Cancelled: " . $reason : 'Cancelled'
        ]);
    }

    public function getOrdersByUser(int $userId): Collection
    {
        $cacheKey = "user_orders_{$userId}";

        return Cache::remember($cacheKey, 1800, function () use ($userId) {
            return $this->orderRepository->getOrdersByUser($userId);
        });
    }

    public function getOrdersByStatus(string $status): Collection
    {
        $cacheKey = "orders_by_status_{$status}";

        return Cache::remember($cacheKey, 900, function () use ($status) {
            return $this->orderRepository->getOrdersByStatus($status);
        });
    }

    public function getOrdersRequiringApproval(int $userId = null): Collection
    {
        $cacheKey = $userId ? "orders_approval_{$userId}" : "orders_approval_all";

        return Cache::remember($cacheKey, 600, function () use ($userId) {
            $orders = $this->orderRepository->getOrdersRequiringApproval();

            if ($userId) {
                // Filter orders where the user is the seller (approver)
                return $orders->where('seller_id', $userId);
            }

            return $orders;
        });
    }

    public function getOrderAnalytics(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        $cacheKey = "order_analytics_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return $this->orderRepository->getOrderAnalytics($startDate, $endDate);
        });
    }

    public function calculateOrderValue(array $items): float
    {
        $totalValue = 0;

        foreach ($items as $item) {
            $resource = Resource::find($item['resource_id']);
            if ($resource) {
                $totalValue += $resource->unit_cost * $item['quantity'];
            }
        }

        return round($totalValue, 2);
    }

    public function checkInventoryAvailability(array $items): array
    {
        $available = true;
        $unavailableItems = [];

        foreach ($items as $item) {
            $resource = Resource::find($item['resource_id']);
            if (!$resource) {
                $available = false;
                $unavailableItems[] = "Resource ID {$item['resource_id']} not found";
                continue;
            }

            $stock = $this->inventoryService->getStockLevel($resource);

            if ($stock < $item['quantity']) {
                $available = false;
                $unavailableItems[] = $resource->name;
            }
        }

        return [
            'available' => $available,
            'unavailable_items' => $unavailableItems
        ];
    }

    public function getOrderWorkflow(int $orderId): array
    {
        return $this->orderRepository->getOrderWorkflow($orderId);
    }

    public function assignOrderToWarehouse(int $orderId, int $warehouseId): bool
    {
        $order = Order::findOrFail($orderId);
        $warehouse = $this->warehouseService->getAllWarehouses()->find($warehouseId);

        if (!$warehouse) {
            return false;
        }

        $order->update(['assigned_warehouse_id' => $warehouseId]);

        Log::info("Order assigned to warehouse", ['order_id' => $orderId, 'warehouse_id' => $warehouseId]);

        return true;
    }

    public function processOrderShipment(int $orderId, array $shipmentData): Order
    {
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $shipmentData['tracking_number'] ?? null,
            'shipping_carrier' => $shipmentData['carrier'] ?? null,
            'estimated_delivery' => $shipmentData['estimated_delivery'] ?? null
        ]);

        $this->clearOrderCaches();

        Log::info("Order shipped", ['order_id' => $orderId, 'tracking_number' => $shipmentData['tracking_number'] ?? null]);

        return $order->fresh(['buyer', 'seller']);
    }

    public function getSupplyChainOrders(int $userId): array
    {
        $user = User::findOrFail($userId);

        $cacheKey = "supply_chain_orders_{$userId}_{$user->role}";

        return Cache::remember($cacheKey, 1200, function () use ($userId, $user) {
            return $this->orderRepository->getSupplyChainOrders($userId, $user->role);
        });
    }

    private function processOrderCompletion(Order $order): void
    {
        // Release any remaining reservations and update final inventory
        $items = $order->getItemsAsArray();

        foreach ($items as $item) {
            $this->inventoryService->confirmDelivery(
                $item['resource_id'],
                $item['quantity'],
                "Order #{$order->id} completed"
            );
        }
    }

    private function clearOrderCaches(): void
    {
        $patterns = [
            'orders_by_status_*',
            'user_orders_*',
            'orders_approval_*',
            'order_analytics_*',
            'supply_chain_orders_*'
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}
