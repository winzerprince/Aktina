<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Employee;
use App\Interfaces\Services\OrderServiceInterface;
use App\Interfaces\Repositories\OrderRepositoryInterface;
use App\Notifications\OrderCreated;
use App\Notifications\OrderAccepted;
use App\Notifications\OrderRejected;
use App\Notifications\OrderCancelled;
use App\Notifications\OrderProcessing;
use App\Notifications\OrderPartiallyFulfilled;
use App\Notifications\OrderFulfilled;
use App\Notifications\OrderShipped;
use App\Notifications\OrderDelivered;
use App\Notifications\OrderCompleted;
use App\Notifications\OrderReturned;
use App\Notifications\LowStockAlert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderService implements OrderServiceInterface
{
    protected OrderRepositoryInterface $orderRepository;

    /**
     * Create a new service instance.
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get all orders
     */
    public function getAllOrders(): Collection
    {
        return $this->orderRepository->getAllOrders();
    }

    /**
     * Get order by ID
     */
    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->getOrderById($id);
    }

    /**
     * Process a new order
     */
    public function processNewOrder(array $orderData): Order
    {
        DB::beginTransaction();

        try {
            // Create the order
            $order = $this->orderRepository->createOrder($orderData);

            // Send notification
            $this->sendOrderNotification($order, 'created');

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process order: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Accept an order
     */
    public function acceptOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_ACCEPTED);

            if ($success) {
                // Try to select employees for the order, but don't fail if none available
                $employees = $this->selectAvailableEmployees();

                if (!empty($employees)) {
                    // Assign employees to the order if available
                    $employeeIds = array_column($employees, 'id');
                    $this->orderRepository->assignEmployeesToOrder($orderId, $employeeIds);
                }
                // Note: We can still accept the order even if no employees are immediately available
                // Employees can be assigned later through the management interface

                // Send notification
                $order = $this->getOrderById($orderId);
                if ($order) {
                    $this->sendOrderNotification($order, 'accepted');
                }

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to accept order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject an order
     */
    public function rejectOrder(int $orderId, string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeRejected()) {
                throw new \Exception('Order cannot be rejected at this stage');
            }

            $additionalData = [];
            if ($reason) {
                $additionalData['rejection_reason'] = $reason;
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus(
                $orderId,
                Order::STATUS_REJECTED,
                $additionalData
            );

            if ($success) {
                // Send notification
                $this->sendOrderNotification($order, 'rejected');

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeCancelled()) {
                throw new \Exception('Order cannot be cancelled at this stage');
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_CANCELLED);

            if ($success) {
                // Send notification
                $this->sendOrderNotification($order, 'cancelled');

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete an order
     */
    public function completeOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeCompleted()) {
                throw new \Exception('Order cannot be completed at this stage');
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_COMPLETE);

            if ($success) {
                // Update product ownership
                $this->orderRepository->updateProductOwnership($orderId);

                // Release employees assigned to this order
                if ($order) {
                    foreach ($order->employees as $employee) {
                        $employee->release();
                    }

                    // Send notification
                    $this->sendOrderNotification($order, 'completed');
                }

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check stock availability for order
     */
    public function checkStockAvailability(array $items): array
    {
        return $this->orderRepository->checkProductStockLevels($items);
    }

    /**
     * Select available employees for order fulfillment
     */
    public function selectAvailableEmployees(int $count = 4): array
    {
        $availableEmployees = Employee::where('status', Employee::STATUS_AVAILABLE)
                                    ->where('current_activity', Employee::ACTIVITY_NONE)
                                    ->inRandomOrder()
                                    ->limit($count)
                                    ->get()
                                    ->toArray();

        return $availableEmployees;
    }

    /**
     * Assign employees to an order
     */
    public function assignEmployeesToOrder(int $orderId, array $employeeIds): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order) {
                throw new \Exception('Order not found');
            }

            if ($order->status !== Order::STATUS_ACCEPTED && $order->status !== Order::STATUS_PENDING) {
                throw new \Exception('Order cannot have employees assigned in its current status');
            }

            // Assign employees through repository
            $success = $this->orderRepository->assignEmployeesToOrder($orderId, $employeeIds);

            if ($success) {
                DB::commit();
                return true;
            }

            throw new \Exception('Failed to assign employees to order');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign employees to order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get orders by user (buyer or seller)
     */
    public function getOrdersByUser(User $user): Collection
    {
        if ($user->isRetailer() || $user->isVendor()) {
            return $this->getBuyerOrders($user);
        } elseif ($user->isAdmin() || $user->isProductionManager()) {
            return $this->getSellerOrders($user);
        }

        return collect([]);
    }

    /**
     * Get buyer's orders
     */
    public function getBuyerOrders(User $user): Collection
    {
        return $this->orderRepository->getOrdersByBuyer($user->id);
    }

    /**
     * Get seller's orders
     */
    public function getSellerOrders(User $user): Collection
    {
        return $this->orderRepository->getOrdersBySeller($user->id);
    }

    /**
     * Get orders by date range with optional filters
     */
    public function getOrdersByDateRange(Carbon $startDate, Carbon $endDate, array $filters = []): Collection
    {
        return $this->orderRepository->getOrdersByDateRange($startDate, $endDate, $filters);
    }

    /**
     * Get order statistics
     */
    public function getOrderStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $orders = $this->getOrdersByDateRange($startDate, $endDate);

        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', Order::STATUS_PENDING)->count();
        $acceptedOrders = $orders->where('status', Order::STATUS_ACCEPTED)->count();
        $completedOrders = $orders->where('status', Order::STATUS_COMPLETE)->count();
        $totalRevenue = $orders->sum('price');

        return [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'accepted_orders' => $acceptedOrders,
            'completed_orders' => $completedOrders,
            'total_revenue' => $totalRevenue,
        ];
    }

    /**
     * Generate notifications for orders
     */
    public function sendOrderNotification(Order $order, string $type): void
    {
        try {
            switch ($type) {
                case 'created':
                    $order->seller->notify(new OrderCreated($order));
                    break;

                case 'accepted':
                    $order->buyer->notify(new OrderAccepted($order));
                    break;

                case 'rejected':
                    $order->buyer->notify(new OrderRejected($order));
                    break;

                case 'cancelled':
                    $order->seller->notify(new OrderCancelled($order));
                    break;

                case 'processing':
                    $order->buyer->notify(new OrderProcessing($order));
                    break;

                case 'partially_fulfilled':
                    $order->buyer->notify(new OrderPartiallyFulfilled($order));
                    break;

                case 'fulfilled':
                    $order->buyer->notify(new OrderFulfilled($order));
                    break;

                case 'shipped':
                    $order->buyer->notify(new OrderShipped($order));
                    break;

                case 'delivered':
                    $order->seller->notify(new OrderDelivered($order));
                    $order->buyer->notify(new OrderDelivered($order));
                    break;

                case 'completed':
                    $order->seller->notify(new OrderCompleted($order));
                    $order->buyer->notify(new OrderCompleted($order));
                    break;

                case 'returned':
                    $order->seller->notify(new OrderReturned($order));
                    break;

                case 'low_stock':
                    // Find all production managers to notify about low stock
                    $productionManagers = User::where('role', 'production_manager')->get();
                    Notification::send($productionManagers, new LowStockAlert($order));
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order notification: ' . $e->getMessage());
        }
    }

    /**
     * Get recent orders by vendor
     */
    public function getRecentOrdersByVendor($vendorId, $limit = 10)
    {
        return Order::where('seller_id', $vendorId)
            ->with(['buyer'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // Production Manager specific methods
    public function getFulfillmentRate($timeframe)
    {
        $totalOrders = Order::where('created_at', '>=', $timeframe)->count();
        $fulfilledOrders = Order::where('created_at', '>=', $timeframe)
            ->whereIn('status', ['complete', 'completed'])->count();

        return $totalOrders > 0 ? ($fulfilledOrders / $totalOrders) * 100 : 0;
    }

    public function getAverageFulfillmentTime($timeframe)
    {
        $orders = Order::where('created_at', '>=', $timeframe)
            ->whereNotNull('completed_at')
            ->select('created_at', 'completed_at')
            ->get();

        if ($orders->isEmpty()) {
            return 0;
        }

        $totalHours = $orders->sum(function($order) {
            return $order->created_at->diffInHours($order->completed_at);
        });

        return round($totalHours / $orders->count(), 1);
    }

    public function getOnTimeDeliveryRate($timeframe)
    {
        $totalDelivered = Order::where('created_at', '>=', $timeframe)
            ->whereNotNull('completed_at')->count();

        $onTimeDelivered = Order::where('created_at', '>=', $timeframe)
            ->whereNotNull('completed_at')
            ->whereNotNull('expected_delivery_date')
            ->whereRaw('completed_at <= expected_delivery_date')
            ->count();

        return $totalDelivered > 0 ? ($onTimeDelivered / $totalDelivered) * 100 : 0;
    }

    public function getPendingOrdersCount()
    {
        return Order::where('status', 'pending')->count();
    }

    public function getCompletedTodayCount()
    {
        return Order::whereDate('completed_at', today())->count();
    }

    public function getFulfillmentTrend($timeframe)
    {
        $days = abs(intval(now()->diffInDays($timeframe)));
        $groupBy = $days <= 7 ? '%Y-%m-%d' : '%Y-%m';

        return Order::where('created_at', '>=', $timeframe)
            ->selectRaw("DATE_FORMAT(created_at, '{$groupBy}') as period")
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('COUNT(CASE WHEN status IN ("complete", "completed") THEN 1 END) as fulfilled_orders')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->mapWithKeys(function ($item) {
                $rate = $item->total_orders > 0 ? ($item->fulfilled_orders / $item->total_orders) * 100 : 0;
                return [$item->period => round($rate, 1)];
            });
    }

    public function getRecentOrdersForProduction($limit = 20)
    {
        return Order::with(['buyer', 'seller'])
            ->whereIn('status', ['pending', 'accepted', 'processing'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Process an order (move to processing state)
     */
    public function processOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeProcessed()) {
                throw new \Exception('Order cannot be processed at this stage');
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_PROCESSING);

            if ($success) {
                // Send notification
                $this->sendOrderNotification($order, 'processing');

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fulfill an order (prepare for shipping)
     */
    public function fulfillOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeFulfilled()) {
                throw new \Exception('Order cannot be fulfilled at this stage');
            }

            // Check stock availability
            $items = $order->getItemsAsArray();
            $stockCheck = $this->checkStockAvailability($items);

            // Verify all items are available
            $allAvailable = true;
            foreach ($stockCheck as $item) {
                if (!$item['in_stock'] || $item['available'] < $item['requested']) {
                    $allAvailable = false;
                    break;
                }
            }

            if (!$allAvailable) {
                throw new \Exception('Cannot fulfill order - some items are out of stock');
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_FULFILLED);

            if ($success) {
                // Update fulfillment data
                $fulfillmentData = [
                    'fulfillment_data' => [
                        'fulfilled_at' => now()->toIso8601String(),
                        'fulfilled_items' => $items,
                    ]
                ];

                $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_FULFILLED, $fulfillmentData);

                // Send notification
                $this->sendOrderNotification($order, 'fulfilled');

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to fulfill order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Partially fulfill an order
     */
    public function partiallyFulfillOrder(int $orderId, array $fulfilledItems): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeFulfilled()) {
                throw new \Exception('Order cannot be partially fulfilled at this stage');
            }

            // Validate fulfilled items against order items
            $orderItems = $order->getItemsAsArray();
            $validItems = true;

            foreach ($fulfilledItems as $fulfilledItem) {
                $found = false;
                foreach ($orderItems as $orderItem) {
                    if ($fulfilledItem['product_id'] == $orderItem['product_id']) {
                        if ($fulfilledItem['quantity'] > $orderItem['quantity']) {
                            $validItems = false;
                        }
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $validItems = false;
                    break;
                }
            }

            if (!$validItems) {
                throw new \Exception('Invalid fulfilled items - quantities do not match order');
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_PARTIALLY_FULFILLED);

            if ($success) {
                // Update fulfillment data
                $fulfillmentData = [
                    'fulfillment_data' => [
                        'partially_fulfilled_at' => now()->toIso8601String(),
                        'fulfilled_items' => $fulfilledItems,
                    ]
                ];

                $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_PARTIALLY_FULFILLED, $fulfillmentData);

                // Send notification
                $this->sendOrderNotification($order, 'partially_fulfilled');

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to partially fulfill order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ship an order
     */
    public function shipOrder(int $orderId, array $shippingDetails = []): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeShipped()) {
                throw new \Exception('Order cannot be shipped at this stage');
            }

            // Validate shipping details
            if (empty($shippingDetails['tracking_number'])) {
                throw new \Exception('Tracking number is required for shipping');
            }

            // Prepare additional data
            $additionalData = [
                'tracking_number' => $shippingDetails['tracking_number'],
                'shipping_carrier' => $shippingDetails['shipping_carrier'] ?? 'Default Carrier',
            ];

            // Add estimated delivery if provided
            if (!empty($shippingDetails['estimated_delivery'])) {
                $additionalData['estimated_delivery'] = $shippingDetails['estimated_delivery'];
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_SHIPPED, $additionalData);

            if ($success) {
                // Send notification
                $this->sendOrderNotification($order, 'shipped');

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to ship order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark an order as delivered
     */
    public function deliverOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeDelivered()) {
                throw new \Exception('Order cannot be marked as delivered at this stage');
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_DELIVERED);

            if ($success) {
                $additionalData = [
                    'delivered_at' => now(),
                ];

                $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_DELIVERED, $additionalData);

                // Send notification
                $this->sendOrderNotification($order, 'delivered');

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark order as delivered: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process a return for an order
     */
    public function returnOrder(int $orderId, string $reason = null): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->getOrderById($orderId);

            if (!$order || !$order->canBeReturned()) {
                throw new \Exception('Order cannot be returned at this stage');
            }

            // Prepare additional data
            $additionalData = [
                'returned_at' => now(),
            ];

            if ($reason) {
                $additionalData['return_reason'] = $reason;
            }

            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_RETURNED, $additionalData);

            if ($success) {
                // Send notification
                $this->sendOrderNotification($order, 'returned');

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process order return: ' . $e->getMessage());
            return false;
        }
    }
}
