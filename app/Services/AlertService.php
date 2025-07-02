<?php

namespace App\Services;

use App\Interfaces\Repositories\AlertRepositoryInterface;
use App\Interfaces\Services\AlertServiceInterface;
use App\Models\InventoryAlert;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class AlertService implements AlertServiceInterface
{
    protected $alertRepository;

    public function __construct(AlertRepositoryInterface $alertRepository)
    {
        $this->alertRepository = $alertRepository;
    }

    public function createAlert(Resource $resource, string $alertType, int $threshold, int $currentValue)
    {
        // Check if similar alert already exists
        $existingAlert = $this->alertRepository->getByResource($resource->id)
                            ->where('alert_type', $alertType)
                            ->where('is_active', true)
                            ->where('is_resolved', false)
                            ->first();
        
        if ($existingAlert) {
            // Update existing alert with new values
            $this->alertRepository->resolve($existingAlert->id, [
                'threshold_value' => $threshold,
                'current_value' => $currentValue,
            ]);
            return $existingAlert;
        }
        
        // Create new alert
        $alert = $this->alertRepository->create([
            'resource_id' => $resource->id,
            'warehouse_id' => $resource->warehouse_id,
            'alert_type' => $alertType,
            'threshold_value' => $threshold,
            'current_value' => $currentValue,
            'is_active' => true,
        ]);
        
        // Send notifications for critical alerts
        if ($alertType === 'critical') {
            $this->sendAlertNotifications($alert);
        }
        
        return $alert;
    }
    
    public function resolveAlert(int $alertId, User $user, string $notes = '')
    {
        $alert = $this->alertRepository->findById($alertId);
        
        if (!$alert) {
            return false;
        }
        
        return $this->alertRepository->resolve($alertId, [
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $user->id,
            'notes' => $notes,
        ]);
    }
    
    public function getActiveAlerts(string $alertType = null)
    {
        return $this->alertRepository->getActive($alertType);
    }
    
    public function getCriticalAlerts()
    {
        return $this->alertRepository->getCritical();
    }
    
    public function getAlertsForWarehouse(int $warehouseId)
    {
        return $this->alertRepository->getByWarehouse($warehouseId);
    }
    
    public function getAlertsForResource(int $resourceId)
    {
        return $this->alertRepository->getByResource($resourceId);
    }
    
    public function checkThresholds(Resource $resource)
    {
        // Check for low stock
        if ($resource->isLowStock()) {
            $this->createAlert($resource, 'low_stock', $resource->reorder_level, $resource->available_quantity);
        }
        
        // Check for critical stock
        if ($resource->isCriticalStock()) {
            $this->createAlert($resource, 'critical', (int)($resource->reorder_level * 0.5), $resource->available_quantity);
        }
        
        // Check for overstock
        if ($resource->isOverstock()) {
            $this->createAlert($resource, 'overstock', $resource->overstock_level, $resource->units);
        }
    }
    
    public function sendAlertNotifications(InventoryAlert $alert)
    {
        // Get users who should receive notifications (admin, production managers)
        $users = User::whereIn('role', ['admin', 'production_manager'])->get();
        
        foreach ($users as $user) {
            // Here you would send actual notifications
            // For now, we'll just create a placeholder notification record
            $user->notifications()->create([
                'type' => 'inventory_alert',
                'data' => [
                    'alert_id' => $alert->id,
                    'resource_name' => $alert->resource->name,
                    'alert_type' => $alert->alert_type,
                    'current_value' => $alert->current_value,
                    'threshold_value' => $alert->threshold_value,
                ],
                'read_at' => null,
            ]);
        }
    }
    
    public function getAlertStats()
    {
        return $this->alertRepository->getStats();
    }
}
