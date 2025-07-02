<?php

namespace App\Interfaces\Repositories;

use App\Models\Warehouse;

interface WarehouseRepositoryInterface
{
    public function all();
    
    public function findById(int $id): ?Warehouse;
    
    public function getActive();
    
    public function getByType(string $type);
    
    public function create(array $data): Warehouse;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function updateUsage(int $id, int $usage);
    
    public function getUtilization(int $id);
}
