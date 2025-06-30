<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Employee;
use App\Interfaces\Services\OrderServiceInterface;
use App\Interfaces\Repositories\OrderRepositoryInterface;
use App\Notifications\OrderCreated;
use App\Notifications\OrderAccepted;
use App\Notifications\OrderCompleted;
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
                // Select employees for the order
                $employees = $this->selectAvailableEmployees();

                if (empty($employees)) {
                    throw new \Exception('No employees available to fulfill the order');
                }

                // Assign employees to the order
                $employeeIds = array_column($employees, 'id');
                $this->orderRepository->assignEmployeesToOrder($orderId, $employeeIds);

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
     * Complete an order
     */
    public function completeOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            // Update the order status
            $success = $this->orderRepository->updateOrderStatus($orderId, Order::STATUS_COMPLETE);

            if ($success) {
                // Update product ownership
                $this->orderRepository->updateProductOwnership($orderId);

                // Release employees assigned to this order
                $order = $this->getOrderById($orderId);
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

                case 'completed':
                    $order->seller->notify(new OrderCompleted($order));
                    $order->buyer->notify(new OrderCompleted($order));
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
}
