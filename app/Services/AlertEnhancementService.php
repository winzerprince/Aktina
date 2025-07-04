<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Resource;
use App\Models\InventoryAlert;
use App\Models\SystemPerformance;
use App\Interfaces\Services\AlertServiceInterface;
use App\Interfaces\Services\AlertEnhancementServiceInterface;
use App\Notifications\LowStockAlert;
use App\Notifications\OrderApprovalRequest;
use App\Notifications\SystemPerformanceAlert;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class AlertEnhancementService implements AlertEnhancementServiceInterface
{
    protected $alertService;
    protected $thresholds = [
        'products' => [
            'critical' => 5,   // Critical threshold for products
            'warning' => 15,   // Warning threshold for products
        ],
        'resources' => [
            'critical' => 3,   // Critical threshold for resources
            'warning' => 10,   // Warning threshold for resources
        ],
        'performance' => [
            'cpu_usage' => 80,       // CPU usage percentage threshold
            'memory_usage' => 85,    // Memory usage percentage threshold
            'disk_usage' => 90,      // Disk usage percentage threshold
            'response_time' => 2000  // Response time threshold in milliseconds
        ],
    ];

    public function __construct(AlertServiceInterface $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Send inventory alert email notifications
     *
     * @param InventoryAlert|EloquentCollection $alerts
     * @return void
     */
    public function sendInventoryAlertEmails($alerts): void
    {
        try {
            // Handle both single alerts and collections
            $alertsCollection = is_a($alerts, InventoryAlert::class) ? collect([$alerts]) : $alerts;
            
            // Find users who should receive inventory alerts
            $recipients = User::whereIn('role', ['admin', 'production_manager', 'supplier'])
                            ->where('notification_preferences->inventory_alerts', true)
                            ->orWhereIn('role', ['admin', 'production_manager']) // Include admins and PMs for testing
                            ->get();

            if ($recipients->isEmpty()) {
                Log::info('No recipients found for inventory alerts');
                return;
            }

            // Send notifications for each alert
            foreach ($alertsCollection as $alert) {
                Notification::send($recipients, new LowStockAlert($alert));
                
                Log::info('Inventory alert emails sent', [
                    'alert_id' => $alert->id,
                    'recipients_count' => $recipients->count()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send inventory alert emails: ' . $e->getMessage(), [
                'alert_id' => $alert->id,
                'exception' => $e
            ]);
        }
    }

    /**
     * Send order approval notification
     *
     * @param Order $order
     * @param User|null $approver
     * @return void
     */
    public function sendOrderApprovalNotification(Order $order, ?User $approver = null): void
    {
        try {
            // Use provided approver or find appropriate approvers
            $approvers = $approver ? collect([$approver]) : $this->findOrderApprovers($order);

            if ($approvers->isEmpty()) {
                Log::warning('No approvers found for order', ['order_id' => $order->id]);
                return;
            }

            // Send notifications to all appropriate approvers
            Notification::send($approvers, new OrderApprovalRequest($order));
            
            // Update order status to indicate it's awaiting approval
            $order->update([
                'status' => 'pending_approval',
                'approval_requested_at' => now()
            ]);
            
            Log::info('Order approval notifications sent', [
                'order_id' => $order->id,
                'approvers_count' => $approvers->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order approval notifications: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e
            ]);
        }
    }

    /**
     * Monitor system performance metrics and send alerts when thresholds are exceeded
     *
     * @param array $metrics Optional metrics array. If not provided, will fetch current metrics.
     * @return SystemPerformance
     */
    public function monitorSystemPerformance(array $metrics = []): SystemPerformance
    {
        try {
            // Use provided metrics or get current ones
            $metrics = !empty($metrics) ? $metrics : $this->getSystemMetrics();
            
            $alerts = [];
            
            // Check each metric against thresholds
            if ($metrics['cpu_usage'] > $this->thresholds['performance']['cpu_usage']) {
                $alerts[] = "CPU usage is at {$metrics['cpu_usage']}%, exceeding threshold of {$this->thresholds['performance']['cpu_usage']}%";
            }
            
            if ($metrics['memory_usage'] > $this->thresholds['performance']['memory_usage']) {
                $alerts[] = "Memory usage is at {$metrics['memory_usage']}%, exceeding threshold of {$this->thresholds['performance']['memory_usage']}%";
            }
            
            if ($metrics['disk_usage'] > $this->thresholds['performance']['disk_usage']) {
                $alerts[] = "Disk usage is at {$metrics['disk_usage']}%, exceeding threshold of {$this->thresholds['performance']['disk_usage']}%";
            }
            
            if ($metrics['response_time'] > $this->thresholds['performance']['response_time']) {
                $alerts[] = "Response time is {$metrics['response_time']}ms, exceeding threshold of {$this->thresholds['performance']['response_time']}ms";
            }
            
            // Save performance record
            $performance = SystemPerformance::create([
                'cpu_usage' => $metrics['cpu_usage'],
                'memory_usage' => $metrics['memory_usage'],
                'disk_usage' => $metrics['disk_usage'],
                'response_time' => $metrics['response_time'],
                'has_alerts' => !empty($alerts),
                'alert_messages' => !empty($alerts) ? $alerts : null
            ]);
            
            // If there are alerts, notify admins
            if (!empty($alerts)) {
                // Find admin users to notify
                $admins = User::where('role', 'admin')
                            ->where('notification_preferences->system_alerts', true)
                            ->orWhere('role', 'admin') // Notify all admins during testing
                            ->get();
                
                // Send notification
                Notification::send($admins, new SystemPerformanceAlert($performance, $alerts));
                
                Log::warning('System performance alerts generated', [
                    'alerts' => $alerts,
                    'performance_id' => $performance->id
                ]);
            }
            
            return $performance;
            
        } catch (\Exception $e) {
            Log::error('Failed to monitor system performance: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            // Create a record with error state
            return SystemPerformance::create([
                'cpu_usage' => $metrics['cpu_usage'] ?? 0,
                'memory_usage' => $metrics['memory_usage'] ?? 0,
                'disk_usage' => $metrics['disk_usage'] ?? 0,
                'response_time' => $metrics['response_time'] ?? 0,
                'has_alerts' => true,
                'alert_messages' => ['Error monitoring system: ' . $e->getMessage()]
            ]);
        }
    }

    /**
     * Get the current alert threshold value for a specific metric
     * 
     * @param string $metricName
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getAlertThreshold(string $metricName, $defaultValue = null)
    {
        $parts = explode('.', $metricName);
        
        // Try to get from cache first
        $cacheKey = 'alert_threshold_' . $metricName;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // If not in cache, get from thresholds array
        $value = $this->thresholds;
        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return $defaultValue;
            }
            $value = $value[$part];
        }
        
        return $value;
    }
    
    /**
     * Set or update an alert threshold value
     * 
     * @param string $metricName
     * @param mixed $value
     * @return bool
     */
    public function setAlertThreshold(string $metricName, $value): bool
    {
        try {
            // Cache the new value
            $cacheKey = 'alert_threshold_' . $metricName;
            Cache::put($cacheKey, $value, now()->addDays(30));
            
            // If we had a persistent storage for thresholds, we would update it here
            
            Log::info("Alert threshold updated: {$metricName} = {$value}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update alert threshold: {$metricName}", [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get all defined alert thresholds
     * 
     * @return array
     */
    public function getAllAlertThresholds(): array
    {
        $thresholds = $this->thresholds;
        
        // Update with any cached values
        $this->updateThresholdsWithCachedValues($thresholds);
        
        return $thresholds;
    }
    
    /**
     * Update the thresholds array with any cached values
     *
     * @param array &$thresholds
     * @return void
     */
    protected function updateThresholdsWithCachedValues(array &$thresholds): void
    {
        // Check for cached product thresholds
        if (Cache::has('alert_threshold_products.critical')) {
            $thresholds['products']['critical'] = Cache::get('alert_threshold_products.critical');
        }
        
        if (Cache::has('alert_threshold_products.warning')) {
            $thresholds['products']['warning'] = Cache::get('alert_threshold_products.warning');
        }
        
        // Check for cached resource thresholds
        if (Cache::has('alert_threshold_resources.critical')) {
            $thresholds['resources']['critical'] = Cache::get('alert_threshold_resources.critical');
        }
        
        if (Cache::has('alert_threshold_resources.warning')) {
            $thresholds['resources']['warning'] = Cache::get('alert_threshold_resources.warning');
        }
        
        // Check for cached performance thresholds
        foreach (['cpu_usage', 'memory_usage', 'disk_usage', 'response_time'] as $metric) {
            $cacheKey = 'alert_threshold_performance.' . $metric;
            if (Cache::has($cacheKey)) {
                $thresholds['performance'][$metric] = Cache::get($cacheKey);
            }
        }
    }
    
    /**
     * Select the appropriate user to approve an order
     * 
     * @param Order $order
     * @return User|null
     */
    public function selectOrderApprover(Order $order): ?User
    {
        try {
            // High-value orders require admin approval
            if ($order->total_amount >= 2000) {
                return User::where('role', 'admin')->first();
            }
            
            // Standard orders can be approved by production managers or admins
            return User::whereIn('role', ['admin', 'production_manager'])
                ->inRandomOrder()
                ->first();
                
        } catch (\Exception $e) {
            Log::error("Error selecting order approver: {$e->getMessage()}", [
                'order_id' => $order->id
            ]);
            
            return null;
        }
    }
    
    /**
     * Update alert thresholds for a specific category
     *
     * @param string $category
     * @param string $type
     * @param int $newThreshold
     * @return bool
     */
    public function updateAlertThreshold(string $category, string $type, int $newThreshold): bool
    {
        try {
            // Validate category and type
            if (!isset($this->thresholds[$category]) || !isset($this->thresholds[$category][$type])) {
                Log::error('Invalid threshold category or type', [
                    'category' => $category,
                    'type' => $type
                ]);
                return false;
            }
            
            // Update threshold
            $this->thresholds[$category][$type] = $newThreshold;
            
            // Cache the updated thresholds for persistence
            Cache::put('alert_thresholds', $this->thresholds, now()->addDays(30));
            
            Log::info('Alert threshold updated', [
                'category' => $category,
                'type' => $type,
                'new_threshold' => $newThreshold
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update alert threshold: ' . $e->getMessage(), [
                'category' => $category,
                'type' => $type,
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * Get alert thresholds for all categories
     *
     * @return array
     */
    public function getAlertThresholds(): array
    {
        // Try to get thresholds from cache first
        $cachedThresholds = Cache::get('alert_thresholds');
        
        if ($cachedThresholds) {
            return $cachedThresholds;
        }
        
        return $this->thresholds;
    }

    /**
     * Find appropriate approvers for an order
     *
     * @param Order $order
     * @return Collection
     */
    protected function findOrderApprovers(Order $order): Collection
    {
        $orderAmount = $order->total_amount;
        
        // Define approval rules based on order amount
        if ($orderAmount > 10000) {
            // High value orders need admin approval
            return User::where('role', 'admin')->get();
        } elseif ($orderAmount > 5000) {
            // Medium value orders can be approved by production managers
            return User::whereIn('role', ['admin', 'production_manager'])->get();
        } else {
            // Lower value orders can be approved by vendors
            return User::whereIn('role', ['vendor'])->get();
        }
    }

    /**
     * Get current system performance metrics
     * In a real system, these would be obtained from the server monitoring tools
     *
     * @return array
     */
    protected function getSystemMetrics(): array
    {
        // Simulate system metrics for demonstration
        // In a production environment, these would come from real server monitoring
        
        return [
            'cpu_usage' => rand(30, 95),
            'memory_usage' => rand(40, 95),
            'disk_usage' => rand(50, 95),
            'response_time' => rand(50, 3000) // milliseconds
        ];
    }
}
