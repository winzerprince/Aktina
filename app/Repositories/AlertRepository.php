<?php

namespace App\Repositories;

use App\Interfaces\Repositories\AlertRepositoryInterface;
use App\Models\InventoryAlert;

class AlertRepository implements AlertRepositoryInterface
{
    public function create(array $data): InventoryAlert
    {
        return InventoryAlert::create($data);
    }
    
    public function findById(int $id): ?InventoryAlert
    {
        return InventoryAlert::with(['resource', 'warehouse', 'resolvedBy'])->find($id);
    }
    
    public function getActive(string $alertType = null)
    {
        $query = InventoryAlert::active()->with(['resource', 'warehouse']);
        
        if ($alertType) {
            $query->byType($alertType);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function getCritical()
    {
        return InventoryAlert::critical()
                           ->active()
                           ->with(['resource', 'warehouse'])
                           ->orderBy('created_at', 'desc')
                           ->get();
    }
    
    public function getByWarehouse(int $warehouseId)
    {
        return InventoryAlert::where('warehouse_id', $warehouseId)
                           ->active()
                           ->with(['resource'])
                           ->orderBy('created_at', 'desc')
                           ->get();
    }
    
    public function getByResource(int $resourceId)
    {
        return InventoryAlert::where('resource_id', $resourceId)
                           ->active()
                           ->with(['warehouse'])
                           ->orderBy('created_at', 'desc')
                           ->get();
    }
    
    public function resolve(int $id, array $data): bool
    {
        return InventoryAlert::where('id', $id)->update($data);
    }
    
    public function getStats()
    {
        return [
            'total_active' => InventoryAlert::active()->count(),
            'critical' => InventoryAlert::critical()->active()->count(),
            'low_stock' => InventoryAlert::byType('low_stock')->active()->count(),
            'overstock' => InventoryAlert::byType('overstock')->active()->count(),
            'resolved_today' => InventoryAlert::where('resolved_at', '>=', now()->startOfDay())->count(),
        ];
    }
    
    public function deleteResolved(int $days = 30)
    {
        return InventoryAlert::where('is_resolved', true)
                           ->where('resolved_at', '<=', now()->subDays($days))
                           ->delete();
    }
}
