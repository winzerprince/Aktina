<?php

namespace App\Services;

use App\Interfaces\Repositories\WarehouseRepositoryInterface;
use App\Interfaces\Services\WarehouseServiceInterface;
use App\Models\Warehouse;
use App\Models\Resource;
use App\Models\User;

class WarehouseService implements WarehouseServiceInterface
{
    protected $warehouseRepository;

    public function __construct(WarehouseRepositoryInterface $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    public function getAllWarehouses()
    {
        return $this->warehouseRepository->all();
    }
    
    public function getActiveWarehouses()
    {
        return $this->warehouseRepository->getActive();
    }
    
    public function getWarehousesByType(string $type)
    {
        return $this->warehouseRepository->getByType($type);
    }
    
    public function getWarehouseCapacity(int $warehouseId)
    {
        $warehouse = $this->warehouseRepository->findById($warehouseId);
        return $warehouse ? [
            'total_capacity' => $warehouse->total_capacity,
            'current_usage' => $warehouse->current_usage,
            'available_capacity' => $warehouse->available_capacity,
            'utilization_percentage' => $warehouse->capacity_utilization,
        ] : null;
    }
    
    public function updateWarehouseUsage(int $warehouseId)
    {
        $warehouse = $this->warehouseRepository->findById($warehouseId);
        if ($warehouse) {
            $totalUsage = $warehouse->resources()->sum('units');
            $warehouse->current_usage = $totalUsage;
            $warehouse->updateCapacityUtilization();
            return true;
        }
        return false;
    }
    
    public function canAccommodateQuantity(int $warehouseId, int $quantity): bool
    {
        $warehouse = $this->warehouseRepository->findById($warehouseId);
        return $warehouse ? $warehouse->canAccommodate($quantity) : false;
    }
    
    public function allocateOptimalWarehouse(Resource $resource, int $quantity): ?Warehouse
    {
        // Get warehouses that can handle this resource type
        $warehouses = $this->warehouseRepository->getActive();
        
        $suitableWarehouses = $warehouses->filter(function ($warehouse) use ($quantity) {
            return $warehouse->canAccommodate($quantity);
        });
        
        if ($suitableWarehouses->isEmpty()) {
            return null;
        }
        
        // Prefer warehouse with lowest utilization
        return $suitableWarehouses->sortBy('capacity_utilization')->first();
    }
    
    public function getWarehouseUtilization(int $warehouseId)
    {
        return $this->warehouseRepository->getUtilization($warehouseId);
    }
    
    public function getWarehouseStats(int $warehouseId)
    {
        $warehouse = $this->warehouseRepository->findById($warehouseId);
        
        if (!$warehouse) {
            return null;
        }
        
        $resources = $warehouse->resources();
        
        return [
            'warehouse' => $warehouse,
            'total_resources' => $resources->count(),
            'low_stock_items' => $resources->whereColumn('available_quantity', '<=', 'reorder_level')->count(),
            'overstock_items' => $resources->whereColumn('units', '>=', 'overstock_level')->count(),
            'critical_items' => $resources->whereRaw('available_quantity <= (reorder_level * 0.5)')->count(),
            'capacity_info' => $this->getWarehouseCapacity($warehouseId),
        ];
    }
}
