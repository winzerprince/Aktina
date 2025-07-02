<?php

namespace App\Interfaces\Services;

use App\Models\InventoryAlert;
use App\Models\Resource;
use App\Models\User;

interface AlertServiceInterface
{
    public function createAlert(Resource $resource, string $alertType, int $threshold, int $currentValue);
    
    public function resolveAlert(int $alertId, User $user, string $notes = '');
    
    public function getActiveAlerts(string $alertType = null);
    
    public function getCriticalAlerts();
    
    public function getAlertsForWarehouse(int $warehouseId);
    
    public function getAlertsForResource(int $resourceId);
    
    public function checkThresholds(Resource $resource);
    
    public function sendAlertNotifications(InventoryAlert $alert);
    
    public function getAlertStats();
}
