<?php

namespace App\Repositories;

use App\Interfaces\Repositories\WarehouseRepositoryInterface;
use App\Models\Warehouse;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    public function all()
    {
        return Warehouse::all();
    }
    
    public function findById(int $id): ?Warehouse
    {
        return Warehouse::find($id);
    }
    
    public function getActive()
    {
        return Warehouse::active()->get();
    }
    
    public function getByType(string $type)
    {
        return Warehouse::byType($type)->active()->get();
    }
    
    public function create(array $data): Warehouse
    {
        return Warehouse::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        return Warehouse::where('id', $id)->update($data);
    }
    
    public function delete(int $id): bool
    {
        $warehouse = Warehouse::find($id);
        return $warehouse ? $warehouse->delete() : false;
    }
    
    public function updateUsage(int $id, int $usage)
    {
        $warehouse = Warehouse::find($id);
        if ($warehouse) {
            $warehouse->current_usage = $usage;
            $warehouse->updateCapacityUtilization();
        }
    }
    
    public function getUtilization(int $id)
    {
        $warehouse = Warehouse::find($id);
        return $warehouse ? $warehouse->capacity_utilization : 0;
    }
}
