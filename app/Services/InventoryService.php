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
        return \App\Models\Product::where('owner_id', $vendorId)->count();
    }
    
    public function getLowStockCountByVendor($vendorId)
    {
        return \App\Models\Product::where('owner_id', $vendorId)
            ->where('stock_quantity', '<=', \DB::raw('reorder_level'))
            ->count();
    }
    
    public function getOutOfStockCountByVendor($vendorId)
    {
        return \App\Models\Product::where('owner_id', $vendorId)
            ->where('stock_quantity', '<=', 0)
            ->count();
    }
    
    public function getTotalInventoryValueByVendor($vendorId)
    {
        return \App\Models\Product::where('owner_id', $vendorId)
            ->selectRaw('SUM(stock_quantity * unit_cost) as total_value')
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
        
        // Get total sales (cost of goods sold)
        $orders = \App\Models\Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->get();
        
        $totalCogs = 0;
        foreach ($orders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $product = \App\Models\Product::find($item['product_id'] ?? null);
                    if ($product) {
                        $totalCogs += ($item['quantity'] ?? 0) * $product->unit_cost;
                    }
                }
            }
        }
        
        // Get average inventory value
        $avgInventoryValue = $this->getTotalInventoryValueByVendor($vendorId);
        
        // Calculate turnover rate (annualized)
        $periodMultiplier = 365 / $days;
        return $avgInventoryValue > 0 ? ($totalCogs * $periodMultiplier) / $avgInventoryValue : 0;
    }
}
