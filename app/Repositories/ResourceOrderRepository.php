<?php

namespace App\Repositories;

use App\Models\ResourceOrder;
use App\Models\Resource;
use App\Interfaces\Repositories\ResourceOrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResourceOrderRepository implements ResourceOrderRepositoryInterface
{
    /**
     * Get all resource orders
     */
    public function getAllResourceOrders(): Collection
    {
        return ResourceOrder::with(['buyer', 'seller'])->get();
    }

    /**
     * Get resource order by ID
     */
    public function getResourceOrderById(int $id): ?ResourceOrder
    {
        return ResourceOrder::with(['buyer', 'seller'])->find($id);
    }

    /**
     * Create new resource order
     */
    public function createResourceOrder(array $orderDetails): ResourceOrder
    {
        return ResourceOrder::create($orderDetails);
    }

    /**
     * Update resource order status
     */
    public function updateResourceOrderStatus(int $orderId, string $status): bool
    {
        try {
            $order = ResourceOrder::findOrFail($orderId);
            $order->status = $status;
            return $order->save();
        } catch (\Exception $e) {
            Log::error('Failed to update resource order status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get resource orders by buyer (Aktina)
     */
    public function getResourceOrdersByBuyer(int $buyerId): Collection
    {
        return ResourceOrder::where('buyer_id', $buyerId)
                          ->with(['buyer', 'seller'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    /**
     * Get resource orders by seller (Supplier)
     */
    public function getResourceOrdersBySeller(int $sellerId): Collection
    {
        return ResourceOrder::where('seller_id', $sellerId)
                          ->with(['buyer', 'seller'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    /**
     * Get resource orders by status
     */
    public function getResourceOrdersByStatus(string $status): Collection
    {
        return ResourceOrder::where('status', $status)
                          ->with(['buyer', 'seller'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    /**
     * Check resource stock levels for order items
     */
    public function checkResourceStockLevels(array $items): array
    {
        $results = [];

        foreach ($items as $item) {
            $resourceId = $item['resource_id'] ?? null;
            $quantity = $item['quantity'] ?? 0;

            if ($resourceId) {
                $resource = Resource::find($resourceId);

                if ($resource) {
                    $inStock = $resource->units >= $quantity;
                    $hasWarning = $resource->isLowStock() || ($resource->units - $quantity) < $resource->reorder_level;

                    $results[] = [
                        'resource_id' => $resourceId,
                        'in_stock' => $inStock,
                        'has_warning' => $hasWarning,
                        'requested' => $quantity,
                        'available' => $resource->units,
                    ];
                } else {
                    $results[] = [
                        'resource_id' => $resourceId,
                        'in_stock' => false,
                        'has_warning' => true,
                        'requested' => $quantity,
                        'available' => 0,
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Get resource orders within date range
     */
    public function getResourceOrdersByDateRange(Carbon $startDate, Carbon $endDate, array $filters = []): Collection
    {
        $query = ResourceOrder::whereBetween('created_at', [$startDate, $endDate]);

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
     * Update resource inventory after order completion
     */
    public function updateResourceInventory(int $orderId): bool
    {
        try {
            DB::beginTransaction();

            $order = ResourceOrder::findOrFail($orderId);
            $items = $order->getItemsAsArray();

            // Only update inventory if order is complete
            if ($order->status !== ResourceOrder::STATUS_COMPLETE) {
                throw new \Exception('Cannot update resource inventory - order is not complete');
            }

            // Update resource units
            foreach ($items as $item) {
                $resourceId = $item['resource_id'] ?? null;
                $quantity = $item['quantity'] ?? 0;

                if ($resourceId && $quantity > 0) {
                    $resource = Resource::find($resourceId);
                    if ($resource) {
                        $resource->units += $quantity;
                        $resource->save();
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update resource inventory: ' . $e->getMessage());
            return false;
        }
    }
}
