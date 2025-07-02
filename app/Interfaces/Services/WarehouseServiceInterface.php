<?php

namespace App\Interfaces\Services;

use App\Models\Warehouse;
use App\Models\Resource;
use App\Models\User;

interface WarehouseServiceInterface
{
    public function getAllWarehouses();
    
    public function getActiveWarehouses();
    
    public function getWarehousesByType(string $type);
    
    public function getWarehouseCapacity(int $warehouseId);
    
    public function updateWarehouseUsage(int $warehouseId);
    
    public function canAccommodateQuantity(int $warehouseId, int $quantity): bool;
    
    public function allocateOptimalWarehouse(Resource $resource, int $quantity): ?Warehouse;
    
    public function getWarehouseUtilization(int $warehouseId);
    
    public function getWarehouseStats(int $warehouseId);
}
