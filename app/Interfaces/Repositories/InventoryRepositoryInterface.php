<?php

namespace App\Interfaces\Repositories;

use App\Models\Resource;

interface InventoryRepositoryInterface
{
    public function findById(int $id): ?Resource;
    
    public function getByWarehouse(int $warehouseId);
    
    public function getLowStock(int $warehouseId = null);
    
    public function getOverstock(int $warehouseId = null);
    
    public function getCriticalStock(int $warehouseId = null);
    
    public function updateStock(int $id, array $data): bool;
    
    public function getMovements(int $resourceId = null, int $days = 30);
    
    public function getStockLevel(int $resourceId, int $warehouseId = null);
    
    public function reserveQuantity(int $resourceId, int $quantity): bool;
    
    public function releaseReservedQuantity(int $resourceId, int $quantity);
}
