<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use App\Interfaces\Repositories\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Get all orders
     */
    public function getAllOrders(): Collection
    {
        return \Illuminate\Support\Facades\Cache::remember('orders_all', now()->addMinutes(10), function () {
            return Order::with(['buyer', 'seller'])->get();
        });
    }

    /**
     * Get order by ID
     */
    public function getOrderById(int $id): ?Order
    {
        return \Illuminate\Support\Facades\Cache::remember("order_{$id}", now()->addMinutes(5), function () use ($id) {
            return Order::with(['buyer', 'seller'])->find($id);
        });
    }

    /**
     * Create new order
     */
    public function createOrder(array $orderDetails): Order
    {
        return Order::create($orderDetails);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $orderId, string $status, array $additionalData = []): bool
    {
        try {
            $order = Order::findOrFail($orderId);
            $oldStatus = $order->status;
            $order->status = $status;

            // Update status-specific timestamps (only for fields that exist)
            switch ($status) {
                case Order::STATUS_ACCEPTED:
                    $order->approved_at = now();
                    break;
                case Order::STATUS_REJECTED:
                    $order->rejected_at = now();
                    if (isset($additionalData['rejection_reason'])) {
                        $order->rejection_reason = $additionalData['rejection_reason'];
                    }
                    break;
                case Order::STATUS_PROCESSING:
                    $order->fulfillment_started_at = now();
                    break;
                case Order::STATUS_SHIPPED:
                    $order->shipped_at = now();
                    if (isset($additionalData['tracking_number'])) {
                        $order->tracking_number = $additionalData['tracking_number'];
                    }
                    if (isset($additionalData['shipping_carrier'])) {
                        $order->shipping_carrier = $additionalData['shipping_carrier'];
                    }
                    if (isset($additionalData['estimated_delivery'])) {
                        $order->estimated_delivery = $additionalData['estimated_delivery'];
                    }
                    break;
                case Order::STATUS_COMPLETE:
                    $order->completed_at = now();
                    break;
                case Order::STATUS_FULFILLMENT_FAILED:
                    $order->fulfillment_failed_at = now();
                    if (isset($additionalData['fulfillment_error'])) {
                        $order->fulfillment_error = $additionalData['fulfillment_error'];
                    }
                    break;
            }

            // Update any additional fields (only for fields that exist)
            foreach ($additionalData as $key => $value) {
                if (in_array($key, ['notes', 'delivery_address', 'expected_delivery_date', 'fulfillment_data'])) {
                    $order->{$key} = $value;
                }
            }

            // Clear any cached data related to this order
            $this->clearOrderCache($orderId);

            return $order->save();
        } catch (\Exception $e) {
            Log::error('Failed to update order status: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'status' => $status,
                'additionalData' => $additionalData
            ]);
            return false;
        }
    }

    /**
     * Bulk update order status
     */
    public function bulkUpdateOrderStatus(array $orderIds, string $status, array $additionalData = []): array
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($orderIds as $orderId) {
                $success = $this->updateOrderStatus($orderId, $status, $additionalData);

                if ($success) {
                    $results['success'][] = $orderId;
                } else {
                    $results['failed'][] = $orderId;
                }
            }

            if (empty($results['failed'])) {
                DB::commit();
            } else {
                // If any updates failed, roll back all changes
                DB::rollBack();
                // Clear any cached data that might have been affected
                $this->clearCollectionCache('bulk');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk update order statuses: ' . $e->getMessage(), [
                'orderIds' => $orderIds,
                'status' => $status
            ]);

            // All orders failed in this case
            $results['failed'] = $orderIds;
            $results['success'] = [];
        }

        return $results;
    }

    /**
     * Get orders by buyer
     */
    public function getOrdersByBuyer(int $buyerId): Collection
    {
        return Order::where('buyer_id', $buyerId)
                   ->with(['buyer', 'seller'])
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get orders by seller
     */
    public function getOrdersBySeller(int $sellerId): Collection
    {
        return Order::where('seller_id', $sellerId)
                   ->with(['buyer', 'seller'])
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get orders by status
     */
    public function getOrdersByStatus(string $status): Collection
    {
        return Order::where('status', $status)
                   ->with(['buyer', 'seller'])
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get orders by multiple statuses
     */
    public function getOrdersByStatuses(array $statuses): Collection
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'orders_statuses_' . implode('_', $statuses),
            now()->addMinutes(5),
            function () use ($statuses) {
                return Order::whereIn('status', $statuses)
                       ->with(['buyer', 'seller'])
                       ->orderBy('updated_at', 'desc')
                       ->get();
            }
        );
    }

    /**
     * Check product stock levels for order items
     */
    public function checkProductStockLevels(array $items): array
    {
        $results = [];

        // In a real implementation, this would check against an inventory table
        // For now, we'll simulate it by checking if the product exists
        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = $item['quantity'] ?? 0;

            if ($productId) {
                $product = Product::find($productId);

                // This is a placeholder - in a real system, we would check against actual inventory
                $inStock = $product ? true : false;
                $hasWarning = $product && $quantity > 10; // Just an example threshold

                $results[] = [
                    'product_id' => $productId,
                    'in_stock' => $inStock,
                    'has_warning' => $hasWarning,
                    'requested' => $quantity,
                    'available' => $inStock ? $quantity : 0, // Placeholder
                ];
            }
        }

        return $results;
    }

    /**
     * Assign employees to order
     */
    public function assignEmployeesToOrder(int $orderId, array $employeeIds): bool
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);

            // Update each employee's status and assignment
            foreach ($employeeIds as $employeeId) {
                $employee = Employee::findOrFail($employeeId);
                $employee->assignToOrder($order);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign employees to order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update product ownership when order is complete
     */
    public function updateProductOwnership(int $orderId): bool
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);
            $items = $order->getItemsAsArray();

            // Only update ownership if order is complete
            if ($order->status !== Order::STATUS_COMPLETE) {
                throw new \Exception('Cannot update product ownership - order is not complete');
            }

            // Update product ownership to the buyer
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                if ($productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $product->owner_id = $order->buyer_id;
                        $product->save();
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product ownership: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get orders within date range
     */
    public function getOrdersByDateRange(Carbon $startDate, Carbon $endDate, array $filters = []): Collection
    {
        $query = Order::whereBetween('created_at', [$startDate, $endDate]);

        // Apply any additional filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['buyer_id'])) {
            $query->where('buyer_id', $filters['buyer_id']);
        }

        if (!empty($filters['seller_id'])) {
            $query->where('seller_id', $filters['seller_id']);
        }

        return $query->with(['buyer', 'seller'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get production managers user IDs for caching
     */
    public function getProductionManagerUserIds(): array
    {
        return User::where('role', 'production_manager')->pluck('id')->toArray();
    }

    /**
     * Get orders with a specific status created after a certain date
     */
    public function getRecentOrdersByStatus(string $status, Carbon $afterDate): Collection
    {
        return Order::where('status', $status)
            ->where('created_at', '>=', $afterDate)
            ->with(['buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get orders that can be fulfilled (based on available inventory)
     */
    public function getFulfillableOrders(): Collection
    {
        // Get accepted orders that haven't started fulfillment
        return Order::where('status', Order::STATUS_ACCEPTED)
            ->whereNull('fulfillment_started_at')
            ->with(['buyer', 'seller'])
            ->orderBy('created_at', 'asc') // Oldest first (FIFO)
            ->get();
    }

    /**
     * Get orders that need attention (late, at risk, etc.)
     */
    public function getOrdersNeedingAttention(): Collection
    {
        // Cache for a shorter period (2 minutes) since this is important operational data
        return \Illuminate\Support\Facades\Cache::remember('orders_needing_attention', now()->addMinutes(2), function () {
            $now = now();

            // Get orders that:
            // 1. Have an expected delivery date in the past but aren't delivered/completed
            // 2. Are in fulfillment but haven't been updated in 3 days
            // 3. Have fulfillment failures
            // 4. Have customer priority flag set
            return Order::select(['id', 'buyer_id', 'seller_id', 'status', 'price', 'expected_delivery_date', 'updated_at', 'created_at'])
                ->where(function($query) use ($now) {
                    $query->where(function($q) use ($now) {
                        $q->whereNotNull('expected_delivery_date')
                          ->where('expected_delivery_date', '<', $now)
                          ->whereNotIn('status', [
                              Order::STATUS_DELIVERED,
                              Order::STATUS_COMPLETE,
                              Order::STATUS_CANCELLED,
                              Order::STATUS_RETURNED
                          ]);
                    })->orWhere(function($q) use ($now) {
                        $q->whereIn('status', [
                              Order::STATUS_PROCESSING,
                              Order::STATUS_PARTIALLY_FULFILLED
                          ])
                          ->where('updated_at', '<', $now->copy()->subDays(3));
                    })->orWhere('status', Order::STATUS_FULFILLMENT_FAILED)
                      ->orWhere('is_priority', true);
                })
                ->with(['buyer:id,name,email,phone', 'seller:id,name,email,phone'])
                ->orderByRaw('CASE WHEN is_priority = 1 THEN 0 ELSE 1 END')
                ->orderBy('expected_delivery_date', 'asc')
                ->get();
        });
    }

    /**
     * Get order status timeline (all status changes with timestamps)
     */
    public function getOrderStatusTimeline(int $orderId): array
    {
        $order = Order::findOrFail($orderId);

        $timeline = [];

        if ($order->created_at) {
            $timeline[] = [
                'status' => 'created',
                'timestamp' => $order->created_at,
                'label' => 'Order Created'
            ];
        }

        if ($order->approved_at) {
            $timeline[] = [
                'status' => Order::STATUS_ACCEPTED,
                'timestamp' => $order->approved_at,
                'label' => 'Order Accepted'
            ];
        }

        if ($order->rejected_at) {
            $timeline[] = [
                'status' => Order::STATUS_REJECTED,
                'timestamp' => $order->rejected_at,
                'label' => 'Order Rejected',
                'reason' => $order->rejection_reason
            ];
        }

        if ($order->fulfillment_started_at) {
            $timeline[] = [
                'status' => Order::STATUS_PROCESSING,
                'timestamp' => $order->fulfillment_started_at,
                'label' => 'Fulfillment Started'
            ];
        }

        if ($order->shipped_at) {
            $timeline[] = [
                'status' => Order::STATUS_SHIPPED,
                'timestamp' => $order->shipped_at,
                'label' => 'Order Shipped',
                'tracking' => $order->tracking_number,
                'carrier' => $order->shipping_carrier
            ];
        }

        if ($order->completed_at) {
            $timeline[] = [
                'status' => Order::STATUS_COMPLETE,
                'timestamp' => $order->completed_at,
                'label' => 'Order Completed'
            ];
        }

        if ($order->fulfillment_failed_at) {
            $timeline[] = [
                'status' => Order::STATUS_FULFILLMENT_FAILED,
                'timestamp' => $order->fulfillment_failed_at,
                'label' => 'Fulfillment Failed',
                'reason' => $order->fulfillment_error
            ];
        }

        // Sort by timestamp
        usort($timeline, function($a, $b) {
            return $a['timestamp']->getTimestamp() - $b['timestamp']->getTimestamp();
        });

        return $timeline;
    }

    /**
     * Search orders with various filters
     */
    public function searchOrders(array $filters = []): Collection
    {
        try {
            // Only select the fields we need
            $query = Order::select([
                'id', 'buyer_id', 'seller_id', 'status', 'price',
                'created_at', 'updated_at', 'completed_at', 'shipped_at',
                'tracking_number', 'shipping_carrier', 'delivery_address'
            ]);

            // Apply status filter
            if (!empty($filters['status'])) {
                if (is_array($filters['status'])) {
                    $query->whereIn('status', $filters['status']);
                } else {
                    $query->where('status', $filters['status']);
                }
            }

            // Apply date range filter using whereDate for better index usage
            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            // Apply buyer filter
            if (!empty($filters['buyer_id'])) {
                $query->where('buyer_id', $filters['buyer_id']);
            }

            // Apply seller filter
            if (!empty($filters['seller_id'])) {
                $query->where('seller_id', $filters['seller_id']);
            }

            // Apply price range filter
            if (!empty($filters['min_price'])) {
                $query->where('price', '>=', $filters['min_price']);
            }

            if (!empty($filters['max_price'])) {
                $query->where('price', '<=', $filters['max_price']);
            }

            // Apply priority filter
            if (isset($filters['is_priority'])) {
                $query->where('is_priority', $filters['is_priority']);
            }

            // Apply search term with more efficient LIKE patterns
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    // Use startsWith for ID searches which can use indexes better
                    if (is_numeric($search)) {
                        $q->where('id', $search);
                    }

                    $q->orWhere('delivery_address', 'like', "%{$search}%")
                      ->orWhere('tracking_number', 'like', "%{$search}%")
                      ->orWhereHas('buyer', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "{$search}%");
                      })
                      ->orWhereHas('seller', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "{$search}%");
                      });
                });
            }

            // Apply pagination if requested
            if (!empty($filters['per_page'])) {
                $perPage = min($filters['per_page'], 50); // Cap at 50 for performance
                $page = $filters['page'] ?? 1;
                $query->limit($perPage)->offset(($page - 1) * $perPage);
            }

            // Apply sorting
            $sortField = $filters['sort_field'] ?? 'created_at';
            $sortDirection = $filters['sort_direction'] ?? 'desc';

            // Validate sort field to prevent SQL injection
            $allowedSortFields = ['id', 'status', 'price', 'created_at', 'updated_at', 'completed_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Eager load only what we need
            return $query->with([
                'buyer:id,name,email,phone',
                'seller:id,name,email,phone'
            ])->get();

        } catch (\Exception $e) {
            Log::error('Error in searchOrders: ' . $e->getMessage(), [
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    /**
     * Get aggregated order statistics
     */
    public function getOrderStats(?int $sellerId = null, ?int $buyerId = null, ?int $days = 30): array
    {
        $cacheKey = "order_stats_" . ($sellerId ?? 'all') . "_" . ($buyerId ?? 'all') . "_{$days}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHours(3), function () use ($sellerId, $buyerId, $days) {
            $query = Order::query();

            if ($sellerId) {
                $query->where('seller_id', $sellerId);
            }

            if ($buyerId) {
                $query->where('buyer_id', $buyerId);
            }

            if ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            }

            // Get counts by status
            $statusCounts = $query->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Get total revenue
            $totalRevenue = $query->sum('price');

            // Get average fulfillment time for completed orders
            $avgFulfillmentTime = DB::table('orders')
                ->whereNotNull('completed_at')
                ->whereNotNull('created_at');

            if ($sellerId) {
                $avgFulfillmentTime->where('seller_id', $sellerId);
            }

            if ($buyerId) {
                $avgFulfillmentTime->where('buyer_id', $buyerId);
            }

            if ($days) {
                $avgFulfillmentTime->where('created_at', '>=', now()->subDays($days));
            }

            $avgFulfillmentHours = $avgFulfillmentTime->select(
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours')
            )->value('avg_hours') ?? 0;

            return [
                'total' => array_sum($statusCounts),
                'by_status' => $statusCounts,
                'revenue' => $totalRevenue,
                'avg_fulfillment_hours' => round($avgFulfillmentHours, 1),
            ];
        });
    }

    /**
     * Clear cached data for a specific order
     */
    protected function clearOrderCache(int $orderId): void
    {
        $cacheKeys = [
            "order_{$orderId}",
            "order_timeline_{$orderId}"
        ];

        foreach ($cacheKeys as $key) {
            if (\Illuminate\Support\Facades\Cache::has($key)) {
                \Illuminate\Support\Facades\Cache::forget($key);
            }
        }
    }

    /**
     * Clear cached collection data
     */
    protected function clearCollectionCache(string $type): void
    {
        $cacheKeys = [
            "orders_all",
            "orders_{$type}",
            "orders_needing_attention",
            "orders_fulfillable"
        ];

        foreach ($cacheKeys as $key) {
            if (\Illuminate\Support\Facades\Cache::has($key)) {
                \Illuminate\Support\Facades\Cache::forget($key);
            }
        }
    }
}
