<?php

namespace App\Services;

use App\Interfaces\Repositories\OrderRepositoryInterface;
use App\Interfaces\Services\ProductionOrderServiceInterface;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductionOrderService implements ProductionOrderServiceInterface
{
    protected OrderRepositoryInterface $orderRepository;
    protected OrderService $orderService;

    /**
     * Create a new service instance.
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderService $orderService
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    /**
     * Get all production orders with optional filtering and pagination
     */
    public function getAllProductionOrders(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = 'production_orders_' . md5(json_encode($filters) . $perPage);

        return Cache::remember($cacheKey, 300, function () use ($filters, $perPage) {
            $query = Order::query()
                ->with(['buyer', 'orderItems', 'orderItems.product'])
                ->whereIn('status', [
                    Order::STATUS_ACCEPTED,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_PARTIALLY_FULFILLED,
                    Order::STATUS_FULFILLED
                ]);

            // Apply filters
            if (isset($filters['status']) && $filters['status'] !== 'all') {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('buyer', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if (isset($filters['date_from']) && !empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to']) && !empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            if (isset($filters['priority']) && !empty($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }

            // Sort orders
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortDirection = $filters['sort_direction'] ?? 'desc';
            $query->orderBy($sortBy, $sortDirection);

            return $query->paginate($perPage);
        });
    }

    /**
     * Get production order by ID
     */
    public function getProductionOrderById(int $id): ?Order
    {
        return Cache::remember("production_order_{$id}", 300, function () use ($id) {
            return Order::with(['buyer', 'orderItems', 'orderItems.product'])
                ->whereIn('status', [
                    Order::STATUS_ACCEPTED,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_PARTIALLY_FULFILLED,
                    Order::STATUS_FULFILLED
                ])
                ->find($id);
        });
    }

    /**
     * Get production orders by status
     */
    public function getOrdersByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = "production_orders_status_{$status}_{$perPage}";

        return Cache::remember($cacheKey, 300, function () use ($status, $perPage) {
            return Order::with(['buyer', 'orderItems', 'orderItems.product'])
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });
    }

    /**
     * Get order statistics for production dashboard
     */
    public function getProductionOrderStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = "production_stats_{$startDate->toDateString()}_{$endDate->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            $stats = [
                'total_orders' => 0,
                'processing_orders' => 0,
                'fulfilled_orders' => 0,
                'partially_fulfilled_orders' => 0,
                'efficiency_rate' => 0,
                'avg_fulfillment_time' => 0,
                'orders_by_day' => []
            ];

            // Get base orders query
            $query = Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', [
                    Order::STATUS_ACCEPTED,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_PARTIALLY_FULFILLED,
                    Order::STATUS_FULFILLED
                ]);

            // Total orders
            $stats['total_orders'] = $query->count();

            // Orders by status
            $stats['processing_orders'] = $query->clone()->where('status', Order::STATUS_PROCESSING)->count();
            $stats['fulfilled_orders'] = $query->clone()->where('status', Order::STATUS_FULFILLED)->count();
            $stats['partially_fulfilled_orders'] = $query->clone()->where('status', Order::STATUS_PARTIALLY_FULFILLED)->count();

            // Calculate efficiency rate (fulfilled orders / total orders)
            if ($stats['total_orders'] > 0) {
                $stats['efficiency_rate'] = round(($stats['fulfilled_orders'] / $stats['total_orders']) * 100, 1);
            }

            // Average fulfillment time in hours
            $avgTime = $query->clone()
                ->where('status', Order::STATUS_FULFILLED)
                ->whereNotNull('fulfilled_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, fulfilled_at)) as avg_time'))
                ->first();

            $stats['avg_fulfillment_time'] = $avgTime ? round($avgTime->avg_time, 1) : 0;

            // Orders by day
            $ordersByDay = $query->clone()
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();

            foreach ($ordersByDay as $dayData) {
                $stats['orders_by_day'][$dayData->date] = $dayData->count;
            }

            return $stats;
        });
    }

    /**
     * Update production order status
     */
    public function updateProductionOrderStatus(int $orderId, string $status, array $additionalData = []): bool
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);

            // Validate status transition
            if (!$this->isValidStatusTransition($order->status, $status)) {
                throw new \Exception("Invalid status transition from {$order->status} to {$status}");
            }

            // Use appropriate order service method based on status
            $success = false;
            switch ($status) {
                case Order::STATUS_PROCESSING:
                    $success = $this->orderService->processOrder($orderId);
                    break;

                case Order::STATUS_PARTIALLY_FULFILLED:
                    $fulfilledItems = $additionalData['fulfilled_items'] ?? [];
                    $success = $this->orderService->partiallyFulfillOrder($orderId, $fulfilledItems);
                    break;

                case Order::STATUS_FULFILLED:
                    $success = $this->orderService->fulfillOrder($orderId);
                    break;

                default:
                    throw new \Exception("Unsupported production order status: {$status}");
            }

            if ($success) {
                // Update any additional metadata if provided
                if (!empty($additionalData)) {
                    unset($additionalData['fulfilled_items']); // Already used above

                    $order->metadata = array_merge($order->metadata ?? [], $additionalData);
                    $order->save();
                }

                // Clear cache
                $this->clearProductionOrderCache($orderId);

                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating production order status: " . $e->getMessage(), [
                'order_id' => $orderId,
                'status' => $status,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Bulk update order statuses
     */
    public function bulkUpdateProductionOrders(array $orderIds, string $status): int
    {
        $successCount = 0;

        foreach ($orderIds as $orderId) {
            if ($this->updateProductionOrderStatus($orderId, $status)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Assign production order to employee(s)
     */
    public function assignProductionOrderToEmployees(int $orderId, array $employeeIds): bool
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);

            // Store employee assignments in order metadata
            $metadata = $order->metadata ?? [];
            $metadata['assigned_employees'] = $employeeIds;
            $metadata['assigned_at'] = now()->toDateTimeString();

            $order->metadata = $metadata;
            $success = $order->save();

            if ($success) {
                // Clear cache
                $this->clearProductionOrderCache($orderId);

                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error assigning production order to employees: " . $e->getMessage(), [
                'order_id' => $orderId,
                'employee_ids' => $employeeIds,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get valid next statuses for a production order
     */
    public function getValidNextStatuses(Order $order): array
    {
        $validStatuses = [];

        switch ($order->status) {
            case Order::STATUS_ACCEPTED:
                $validStatuses[Order::STATUS_PROCESSING] = 'Start Processing';
                break;

            case Order::STATUS_PROCESSING:
                $validStatuses[Order::STATUS_PARTIALLY_FULFILLED] = 'Partially Fulfill';
                $validStatuses[Order::STATUS_FULFILLED] = 'Fulfill Completely';
                break;

            case Order::STATUS_PARTIALLY_FULFILLED:
                $validStatuses[Order::STATUS_FULFILLED] = 'Complete Fulfillment';
                break;
        }

        return $validStatuses;
    }

    /**
     * Schedule production for an order
     */
    public function scheduleProduction(int $orderId, array $productionDetails): bool
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);

            // Store production schedule in order metadata
            $metadata = $order->metadata ?? [];
            $metadata['production_schedule'] = $productionDetails;
            $metadata['scheduled_at'] = now()->toDateTimeString();

            $order->metadata = $metadata;
            $success = $order->save();

            if ($success) {
                // Clear cache
                $this->clearProductionOrderCache($orderId);

                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error scheduling production: " . $e->getMessage(), [
                'order_id' => $orderId,
                'production_details' => $productionDetails,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Check resource availability for production
     */
    public function checkResourceAvailability(int $orderId): array
    {
        try {
            $order = Order::with(['orderItems', 'orderItems.product'])->findOrFail($orderId);
            $resources = [];

            // This is a simplified implementation - in a real system, you would check
            // inventory levels for each required resource for all order items
            foreach ($order->orderItems as $item) {
                // Get required resources for this product
                // This would typically come from a BOM (Bill of Materials) table
                // For now, we'll simulate some resource requirements
                $resourcesNeeded = $this->simulateResourcesNeeded($item->product_id, $item->quantity);

                foreach ($resourcesNeeded as $resource => $needed) {
                    if (!isset($resources[$resource])) {
                        $resources[$resource] = [
                            'name' => $resource,
                            'required' => 0,
                            'available' => $this->simulateResourceAvailability($resource),
                            'sufficient' => true
                        ];
                    }

                    $resources[$resource]['required'] += $needed;

                    if ($resources[$resource]['required'] > $resources[$resource]['available']) {
                        $resources[$resource]['sufficient'] = false;
                    }
                }
            }

            return array_values($resources);
        } catch (\Exception $e) {
            Log::error("Error checking resource availability: " . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Validate if a status transition is valid
     */
    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $validTransitions = [
            Order::STATUS_ACCEPTED => [
                Order::STATUS_PROCESSING
            ],
            Order::STATUS_PROCESSING => [
                Order::STATUS_PARTIALLY_FULFILLED,
                Order::STATUS_FULFILLED
            ],
            Order::STATUS_PARTIALLY_FULFILLED => [
                Order::STATUS_FULFILLED
            ]
        ];

        return isset($validTransitions[$currentStatus]) &&
               in_array($newStatus, $validTransitions[$currentStatus]);
    }

    /**
     * Clear production order cache
     */
    private function clearProductionOrderCache(int $orderId): void
    {
        Cache::forget("production_order_{$orderId}");
        Cache::forget('production_orders_all');

        // Clear status-based caches
        foreach ([Order::STATUS_ACCEPTED, Order::STATUS_PROCESSING, Order::STATUS_PARTIALLY_FULFILLED, Order::STATUS_FULFILLED] as $status) {
            Cache::forget("production_orders_status_{$status}_15");
        }

        // Clear statistics cache
        $today = Carbon::today();
        $lastMonth = Carbon::today()->subMonth();
        Cache::forget("production_stats_{$lastMonth->toDateString()}_{$today->toDateString()}");
    }

    /**
     * Simulate resources needed for a product
     * In a real implementation, this would come from a BOM table
     */
    private function simulateResourcesNeeded(int $productId, int $quantity): array
    {
        // Simulate some resource requirements based on product ID
        $baseResources = [
            'Raw Material A' => 2,
            'Raw Material B' => 1,
            'Component C' => 3
        ];

        // Scale by quantity
        return array_map(function ($value) use ($quantity) {
            return $value * $quantity;
        }, $baseResources);
    }

    /**
     * Simulate resource availability
     * In a real implementation, this would check inventory levels
     */
    private function simulateResourceAvailability(string $resource): int
    {
        // Simulate availability
        $availabilityMap = [
            'Raw Material A' => 100,
            'Raw Material B' => 80,
            'Component C' => 50
        ];

        return $availabilityMap[$resource] ?? 0;
    }
}
