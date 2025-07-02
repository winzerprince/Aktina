<?php

namespace App\Interfaces\Services;

use App\Models\Resource;
use App\Models\User;
use App\Models\Warehouse;

interface InventoryServiceInterface
{
    public function updateStock(Resource $resource, int $quantity, string $movementType, User $user, array $options = []);
    
    public function reserveStock(Resource $resource, int $quantity): bool;
    
    public function releaseReservedStock(Resource $resource, int $quantity);
    
    public function transferStock(Resource $resource, Warehouse $fromWarehouse, Warehouse $toWarehouse, int $quantity, User $user);
    
    public function adjustStock(Resource $resource, int $newQuantity, User $user, string $reason = '');
    
    public function getLowStockItems(int $warehouseId = null);
    
    public function getOverstockItems(int $warehouseId = null);
    
    public function getCriticalStockItems(int $warehouseId = null);
    
    public function getInventoryMovements(Resource $resource = null, int $days = 30);
    
    public function calculateAverageCost(Resource $resource);
    
    public function getStockLevel(Resource $resource, Warehouse $warehouse = null);
}
