<?php

namespace App\Interfaces\Services;

use App\Models\InventoryAlert;
use App\Models\Order;
use App\Models\SystemPerformance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AlertEnhancementServiceInterface
{
    /**
     * Send inventory alert email notifications
     * 
     * @param InventoryAlert|Collection $alerts
     * @return void
     */
    public function sendInventoryAlertEmails($alerts): void;
    
    /**
     * Send order approval notification to approver
     * 
     * @param Order $order
     * @param User|null $approver
     * @return void
     */
    public function sendOrderApprovalNotification(Order $order, ?User $approver = null): void;
    
    /**
     * Record system performance metrics and send alert if thresholds are exceeded
     * 
     * @param array $metrics
     * @return SystemPerformance
     */
    public function monitorSystemPerformance(array $metrics): SystemPerformance;
    
    /**
     * Get the current alert threshold value for a specific metric
     * 
     * @param string $metricName
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getAlertThreshold(string $metricName, $defaultValue = null);
    
    /**
     * Set or update an alert threshold value
     * 
     * @param string $metricName
     * @param mixed $value
     * @return bool
     */
    public function setAlertThreshold(string $metricName, $value): bool;
    
    /**
     * Get all defined alert thresholds
     * 
     * @return array
     */
    public function getAllAlertThresholds(): array;
    
    /**
     * Select the appropriate user to approve an order
     * 
     * @param Order $order
     * @return User|null
     */
    public function selectOrderApprover(Order $order): ?User;
}
