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

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Get all orders
     */
    public function getAllOrders(): Collection
    {
        return Order::with(['buyer', 'seller'])->get();
    }

    /**
     * Get order by ID
     */
    public function getOrderById(int $id): ?Order
    {
        return Order::with(['buyer', 'seller'])->find($id);
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
    public function updateOrderStatus(int $orderId, string $status): bool
    {
        try {
            $order = Order::findOrFail($orderId);
            $order->status = $status;
            return $order->save();
        } catch (\Exception $e) {
            Log::error('Failed to update order status: ' . $e->getMessage());
            return false;
        }
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
}
