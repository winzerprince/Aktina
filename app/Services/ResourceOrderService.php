<?php

namespace App\Services;

use App\Models\ResourceOrder;
use App\Models\User;
use App\Interfaces\Services\ResourceOrderServiceInterface;
use App\Interfaces\Repositories\ResourceOrderRepositoryInterface;
use App\Notifications\ResourceOrderCreated;
use App\Notifications\ResourceOrderAccepted;
use App\Notifications\ResourceOrderCompleted;
use App\Notifications\ResourceLowStockAlert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ResourceOrderService implements ResourceOrderServiceInterface
{
    protected ResourceOrderRepositoryInterface $resourceOrderRepository;

    /**
     * Create a new service instance.
     */
    public function __construct(ResourceOrderRepositoryInterface $resourceOrderRepository)
    {
        $this->resourceOrderRepository = $resourceOrderRepository;
    }

    /**
     * Get all resource orders
     */
    public function getAllResourceOrders(): Collection
    {
        return $this->resourceOrderRepository->getAllResourceOrders();
    }

    /**
     * Get resource order by ID
     */
    public function getResourceOrderById(int $id): ?ResourceOrder
    {
        return $this->resourceOrderRepository->getResourceOrderById($id);
    }

    /**
     * Process a new resource order
     */
    public function processNewResourceOrder(array $orderData): ResourceOrder
    {
        DB::beginTransaction();

        try {
            // Create the resource order
            $order = $this->resourceOrderRepository->createResourceOrder($orderData);

            // Send notification
            $this->sendResourceOrderNotification($order, 'created');

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process resource order: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Accept a resource order
     */
    public function acceptResourceOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            // Update the resource order status
            $success = $this->resourceOrderRepository->updateResourceOrderStatus($orderId, ResourceOrder::STATUS_ACCEPTED);

            if ($success) {
                // Send notification
                $order = $this->getResourceOrderById($orderId);
                if ($order) {
                    $this->sendResourceOrderNotification($order, 'accepted');
                }

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update resource order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to accept resource order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete a resource order
     */
    public function completeResourceOrder(int $orderId): bool
    {
        DB::beginTransaction();

        try {
            // Update the resource order status
            $success = $this->resourceOrderRepository->updateResourceOrderStatus($orderId, ResourceOrder::STATUS_COMPLETE);

            if ($success) {
                // Update resource inventory
                $this->resourceOrderRepository->updateResourceInventory($orderId);

                // Send notification
                $order = $this->getResourceOrderById($orderId);
                if ($order) {
                    $this->sendResourceOrderNotification($order, 'completed');
                }

                DB::commit();
                return true;
            }

            throw new \Exception('Failed to update resource order status');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete resource order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check stock availability for resource order
     */
    public function checkResourceStockAvailability(array $items): array
    {
        return $this->resourceOrderRepository->checkResourceStockLevels($items);
    }

    /**
     * Get resource orders by user (buyer or seller)
     */
    public function getResourceOrdersByUser(User $user): Collection
    {
        if ($user->isAdmin() || $user->isProductionManager()) {
            return $this->getAktinaResourceOrders($user);
        } elseif ($user->isSupplier()) {
            return $this->getSupplierResourceOrders($user);
        }

        return collect([]);
    }

    /**
     * Get Aktina's resource orders (as buyer)
     */
    public function getAktinaResourceOrders(User $user): Collection
    {
        return $this->resourceOrderRepository->getResourceOrdersByBuyer($user->id);
    }

    /**
     * Get supplier's resource orders (as seller)
     */
    public function getSupplierResourceOrders(User $user): Collection
    {
        return $this->resourceOrderRepository->getResourceOrdersBySeller($user->id);
    }

    /**
     * Get resource orders by date range with optional filters
     */
    public function getResourceOrdersByDateRange(Carbon $startDate, Carbon $endDate, array $filters = []): Collection
    {
        return $this->resourceOrderRepository->getResourceOrdersByDateRange($startDate, $endDate, $filters);
    }

    /**
     * Get resource order statistics
     */
    public function getResourceOrderStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $orders = $this->getResourceOrdersByDateRange($startDate, $endDate);

        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', ResourceOrder::STATUS_PENDING)->count();
        $acceptedOrders = $orders->where('status', ResourceOrder::STATUS_ACCEPTED)->count();
        $completedOrders = $orders->where('status', ResourceOrder::STATUS_COMPLETE)->count();
        $totalCost = $orders->sum('price');

        return [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'accepted_orders' => $acceptedOrders,
            'completed_orders' => $completedOrders,
            'total_cost' => $totalCost,
        ];
    }

    /**
     * Generate notifications for resource orders
     */
    public function sendResourceOrderNotification(ResourceOrder $order, string $type): void
    {
        try {
            switch ($type) {
                case 'created':
                    $order->seller->notify(new ResourceOrderCreated($order));
                    break;

                case 'accepted':
                    $order->buyer->notify(new ResourceOrderAccepted($order));
                    break;

                case 'completed':
                    $order->seller->notify(new ResourceOrderCompleted($order));
                    $order->buyer->notify(new ResourceOrderCompleted($order));
                    break;

                case 'low_stock':
                    // Find production and admin users to notify about low stock
                    $managers = User::whereIn('role', ['admin', 'production_manager'])->get();
                    Notification::send($managers, new ResourceLowStockAlert($order));
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send resource order notification: ' . $e->getMessage());
        }
    }
}
