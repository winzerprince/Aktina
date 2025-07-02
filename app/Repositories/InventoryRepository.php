<?php

namespace App\Repositories;

use App\Interfaces\Repositories\InventoryRepositoryInterface;
use App\Models\Resource;
use App\Models\InventoryMovement;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function findById(int $id): ?Resource
    {
        return Resource::with(['warehouse', 'inventoryAlerts'])->find($id);
    }
    
    public function getByWarehouse(int $warehouseId)
    {
        return Resource::where('warehouse_id', $warehouseId)
                      ->with(['warehouse', 'inventoryAlerts'])
                      ->get();
    }
    
    public function getLowStock(int $warehouseId = null)
    {
        $query = Resource::whereColumn('available_quantity', '<=', 'reorder_level');
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        return $query->with(['warehouse'])->get();
    }
    
    public function getOverstock(int $warehouseId = null)
    {
        $query = Resource::whereColumn('units', '>=', 'overstock_level');
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        return $query->with(['warehouse'])->get();
    }
    
    public function getCriticalStock(int $warehouseId = null)
    {
        $query = Resource::whereRaw('available_quantity <= (reorder_level * 0.5)');
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        return $query->with(['warehouse'])->get();
    }
    
    public function updateStock(int $id, array $data): bool
    {
        $resource = Resource::find($id);
        if ($resource) {
            $resource->update($data);
            $resource->updateAvailableQuantity();
            return true;
        }
        return false;
    }
    
    public function getMovements(int $resourceId = null, int $days = 30)
    {
        $query = InventoryMovement::with(['resource', 'fromWarehouse', 'toWarehouse', 'movedBy'])
                                 ->where('created_at', '>=', now()->subDays($days));
        
        if ($resourceId) {
            $query->where('resource_id', $resourceId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function getStockLevel(int $resourceId, int $warehouseId = null)
    {
        $query = Resource::where('id', $resourceId);
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        $resource = $query->first();
        return $resource ? $resource->available_quantity : 0;
    }
    
    public function reserveQuantity(int $resourceId, int $quantity): bool
    {
        $resource = Resource::find($resourceId);
        return $resource ? $resource->reserveQuantity($quantity) : false;
    }
    
    public function releaseReservedQuantity(int $resourceId, int $quantity)
    {
        $resource = Resource::find($resourceId);
        if ($resource) {
            $resource->releaseReservedQuantity($quantity);
        }
    }
}
