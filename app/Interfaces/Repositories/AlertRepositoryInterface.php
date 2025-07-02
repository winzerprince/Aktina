<?php

namespace App\Interfaces\Repositories;

use App\Models\InventoryAlert;

interface AlertRepositoryInterface
{
    public function create(array $data): InventoryAlert;
    
    public function findById(int $id): ?InventoryAlert;
    
    public function getActive(string $alertType = null);
    
    public function getCritical();
    
    public function getByWarehouse(int $warehouseId);
    
    public function getByResource(int $resourceId);
    
    public function resolve(int $id, array $data): bool;
    
    public function getStats();
    
    public function deleteResolved(int $days = 30);
}
