<?php

namespace App\Services;

use App\Interfaces\Repositories\InventoryRepositoryInterface;
use App\Interfaces\Services\InventoryServiceInterface;
use App\Interfaces\Services\AlertServiceInterface;
use App\Models\Resource;
use App\Models\User;
use App\Models\Warehouse;

class InventoryService implements InventoryServiceInterface
{
    protected $inventoryRepository;
    protected $alertService;

    public function __construct(
        InventoryRepositoryInterface $inventoryRepository,
        AlertServiceInterface $alertService
    ) {
        $this->inventoryRepository = $inventoryRepository;
        $this->alertService = $alertService;
    }

    public function updateStock(Resource $resource, int $quantity, string $movementType, User $user, array $options = [])
    {
        $resource->recordMovement($movementType, $quantity, $user, $options);
        $this->alertService->checkThresholds($resource);

        return $resource;
    }

    public function reserveStock(Resource $resource, int $quantity): bool
    {
        return $this->inventoryRepository->reserveQuantity($resource->id, $quantity);
    }

    public function releaseReservedStock(Resource $resource, int $quantity)
    {
        $this->inventoryRepository->releaseReservedQuantity($resource->id, $quantity);
        $this->alertService->checkThresholds($resource);
    }

    public function transferStock(Resource $resource, Warehouse $fromWarehouse, Warehouse $toWarehouse, int $quantity, User $user)
    {
        // Check if we have enough stock in source warehouse
        if ($resource->warehouse_id !== $fromWarehouse->id || $resource->available_quantity < $quantity) {
            throw new \Exception('Insufficient stock for transfer');
        }

        // Check if destination warehouse can accommodate
        if (!$toWarehouse->canAccommodate($quantity)) {
            throw new \Exception('Destination warehouse cannot accommodate quantity');
        }

        // Perform transfer
        $this->updateStock($resource, $quantity, 'transfer', $user, [
            'from_warehouse_id' => $fromWarehouse->id,
            'to_warehouse_id' => $toWarehouse->id,
            'reason' => 'Stock transfer between warehouses',
        ]);

        // Update resource warehouse
        $resource->warehouse_id = $toWarehouse->id;
        $resource->save();

        return true;
    }

    public function adjustStock(Resource $resource, int $newQuantity, User $user, string $reason = '')
    {
        $this->updateStock($resource, $newQuantity, 'adjustment', $user, [
            'reason' => $reason ?: 'Stock adjustment',
        ]);

        return $resource;
    }

    public function getLowStockItems(int $warehouseId = null)
    {
        return $this->inventoryRepository->getLowStock($warehouseId);
    }

    public function getOverstockItems(int $warehouseId = null)
    {
        return $this->inventoryRepository->getOverstock($warehouseId);
    }

    public function getCriticalStockItems(int $warehouseId = null)
    {
        return $this->inventoryRepository->getCriticalStock($warehouseId);
    }

    public function getInventoryMovements(Resource $resource = null, int $days = 30)
    {
        return $this->inventoryRepository->getMovements($resource?->id, $days);
    }

    public function calculateAverageCost(Resource $resource)
    {
        // Simple weighted average calculation
        $movements = $resource->inventoryMovements()
                            ->where('movement_type', 'inbound')
                            ->where('created_at', '>=', now()->subDays(90))
                            ->get();

        if ($movements->isEmpty()) {
            return $resource->unit_cost;
        }

        $totalCost = 0;
        $totalQuantity = 0;

        foreach ($movements as $movement) {
            $cost = $movement->metadata['unit_cost'] ?? $resource->unit_cost;
            $totalCost += $cost * $movement->quantity;
            $totalQuantity += $movement->quantity;
        }

        return $totalQuantity > 0 ? $totalCost / $totalQuantity : $resource->unit_cost;
    }

    public function getStockLevel(Resource $resource, Warehouse $warehouse = null)
    {
        return $this->inventoryRepository->getStockLevel($resource->id, $warehouse?->id);
    }

    public function getTotalProductsByVendor($vendorId)
    {
        $vendor = \App\Models\User::find($vendorId);
        if (!$vendor || !$vendor->company_name) {
            return 0;
        }

        return \App\Models\Product::whereRaw("JSON_EXTRACT(company_quantities, '$.\"" . $vendor->company_name . "\"') IS NOT NULL")->count();
    }

    public function getLowStockCountByVendor($vendorId)
    {
        // Since products don't have stock, return resources with low stock
        // This is a conceptual issue - vendors manage products, not resources
        return \App\Models\Resource::where('units', '<=', \DB::raw('reorder_level'))->count();
    }

    public function getOutOfStockCountByVendor($vendorId)
    {
        // Since products don't have stock, return resources out of stock
        return \App\Models\Resource::where('units', '<=', 0)->count();
    }

    public function getTotalInventoryValueByVendor($vendorId)
    {
        // Since products don't have inventory value, return total resource value
        return \App\Models\Resource::selectRaw('SUM(units * unit_cost) as total_value')
            ->value('total_value') ?? 0;
    }

    public function getInventoryTurnoverRate($vendorId, $timeframe = '30d')
    {
        // Calculate inventory turnover rate based on sales vs average inventory
        $days = match($timeframe) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30,
        };

        $startDate = now()->subDays($days);

        // Get total sales (cost of goods sold) - simplified calculation
        $orders = \App\Models\Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $totalCogs = 0;
        foreach ($orders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    // Use a fixed cost per unit since we don't have detailed product cost data
                    $totalCogs += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) * 0.7; // 70% of price as cost
                }
            }
        }

        // Get average inventory value from resources
        $avgInventoryValue = $this->getTotalInventoryValueByVendor($vendorId);

        // Calculate turnover rate (annualized)
        $periodMultiplier = 365 / $days;
        return $avgInventoryValue > 0 ? ($totalCogs * $periodMultiplier) / $avgInventoryValue : 0;
    }

    // Production Manager specific methods
    public function getWarehouses()
    {
        return \App\Models\Warehouse::select('id', 'name', 'location', 'total_capacity', 'current_usage', 'type')
            ->where('is_active', true)
            ->get();
    }

    public function getMaterialUsage($timeframe, $warehouseId = null)
    {
        // Calculate material usage based on inventory movements
        $query = \App\Models\Resource::query();

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->selectRaw('component_type, SUM(units) as total_used')
            ->groupBy('component_type')
            ->get()
            ->pluck('total_used', 'component_type')
            ->toArray();
    }

    public function getTotalItems($warehouseId = null)
    {
        $query = \App\Models\Resource::query();

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->sum('units');
    }

    public function getLowStockCount($warehouseId = null)
    {
        $query = \App\Models\Resource::where('units', '<=', \DB::raw('reorder_level'));

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->count();
    }

    public function getOutOfStockCount($warehouseId = null)
    {
        $query = \App\Models\Resource::where('units', '<=', 0);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->count();
    }

    public function getWarehouseCapacity($warehouseId = null)
    {
        if ($warehouseId) {
            $warehouse = \App\Models\Warehouse::find($warehouseId);
            return $warehouse ? $warehouse->total_capacity : 0;
        }

        return \App\Models\Warehouse::sum('total_capacity');
    }

    public function getCapacityUtilization($warehouseId = null)
    {
        $totalCapacity = $this->getWarehouseCapacity($warehouseId);
        $totalItems = $this->getTotalItems($warehouseId);

        return $totalCapacity > 0 ? ($totalItems / $totalCapacity) * 100 : 0;
    }

    public function getRecentMovements($warehouseId = null, $limit = 10)
    {
        $query = \DB::table('resources')
            ->select('resources.name', 'resources.component_type', 'resources.units', 'resources.updated_at');

        if ($warehouseId) {
            $query->where('resources.warehouse_id', $warehouseId);
        }

        return $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'resource_name' => $item->name,
                    'type' => $item->component_type,
                    'quantity' => $item->units,
                    'timestamp' => $item->updated_at,
                    'movement_type' => 'adjustment', // Simplified
                ];
            });
    }

    public function getInventoryAlerts()
    {
        $lowStockItems = \App\Models\Resource::where('units', '<=', \DB::raw('reorder_level'))
            ->select('id', 'name', 'units', 'reorder_level')
            ->get();

        $outOfStockItems = \App\Models\Resource::where('units', '<=', 0)
            ->select('id', 'name', 'component_type')
            ->get();

        $alerts = [];

        foreach ($lowStockItems as $item) {
            $alerts[] = [
                'id' => 'low_stock_' . $item->id,
                'type' => 'inventory',
                'severity' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => "Low stock for {$item->name}: {$item->units} remaining (reorder at {$item->reorder_level})",
                'timestamp' => now()->diffForHumans(),
                'acknowledged' => false,
            ];
        }

        foreach ($outOfStockItems as $item) {
            $alerts[] = [
                'id' => 'out_of_stock_' . $item->id,
                'type' => 'inventory',
                'severity' => 'critical',
                'title' => 'Out of Stock Alert',
                'message' => "Out of stock: {$item->name} ({$item->component_type})",
                'timestamp' => now()->diffForHumans(),
                'acknowledged' => false,
            ];
        }

        return collect($alerts);
    }

    public function getProductCategories()
    {
        return \App\Models\Product::distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();
    }

    public function getResourceTypes()
    {
        return \App\Models\Resource::distinct()
            ->pluck('component_type')
            ->filter()
            ->sort()
            ->values();
    }

    public function requestResource($resourceId, $quantity)
    {
        $resource = \App\Models\Resource::findOrFail($resourceId);

        if ($resource->available_quantity < $quantity) {
            throw new \Exception("Insufficient stock available. Available: {$resource->available_quantity}, Requested: {$quantity}");
        }

        // Reserve the requested quantity
        $resource->reserveQuantity($quantity);

        // Log the resource request (you may want to create a ResourceRequest model later)
        \Log::info("Resource requested", [
            'resource_id' => $resourceId,
            'quantity' => $quantity,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);

        return true;
    }
}
