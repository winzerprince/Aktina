<?php

namespace Tests\Feature\Integration;

use App\Models\InventoryAlert;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Notifications\LowStockAlert;
use App\Notifications\OrderApprovalRequest;
use App\Notifications\SystemPerformanceAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AlertNotificationsTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function low_stock_alerts_notify_appropriate_users()
    {
        // Create users with different roles
        $admin = User::factory()->create(['role' => 'admin']);
        $productionManager = User::factory()->create(['role' => 'production_manager']);
        $vendor = User::factory()->create(['role' => 'vendor']);
        
        // Create a product
        $product = Product::factory()->create([
            'vendor_id' => $vendor->id,
            'name' => 'Test Product',
            'current_stock' => 3,
            'low_stock_threshold' => 5
        ]);
        
        // Create warehouse
        $warehouse = Warehouse::factory()->create();
        
        // Create a low stock alert
        $alert = InventoryAlert::factory()->create([
            'type' => 'product',
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'current_level' => 3,
            'threshold_level' => 5,
            'severity' => 'critical',
            'resolved' => false
        ]);
        
        // Trigger the alert system
        $this->app->make('App\Interfaces\Services\AlertEnhancementServiceInterface')
            ->sendInventoryAlertEmails($alert);
        
        // Admin should be notified of all critical alerts
        Notification::assertSentTo(
            $admin,
            LowStockAlert::class,
            function ($notification, $channels) use ($product) {
                return $notification->alert->product_id === $product->id;
            }
        );
        
        // Production manager should be notified
        Notification::assertSentTo(
            $productionManager,
            LowStockAlert::class
        );
        
        // Product vendor should be notified
        Notification::assertSentTo(
            $vendor,
            LowStockAlert::class
        );
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function order_approval_notifications_are_sent_to_correct_approvers()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create a high-value order that requires approval
        $order = Order::factory()->create([
            'status' => 'pending_approval',
            'total_amount' => 5000  // High value that requires admin approval
        ]);
        
        // Trigger the notification
        $this->app->make('App\Interfaces\Services\AlertEnhancementServiceInterface')
            ->sendOrderApprovalNotification($order);
        
        // Admin should receive approval request
        Notification::assertSentTo(
            $admin,
            OrderApprovalRequest::class,
            function ($notification, $channels) use ($order) {
                return $notification->order->id === $order->id;
            }
        );
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function system_performance_alerts_notify_admin_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Critical system metrics
        $metrics = [
            'cpu_usage' => 95,      // Critical
            'memory_usage' => 92,   // Critical
            'disk_usage' => 94,     // Critical
            'response_time' => 3500 // Critical
        ];
        
        // Trigger the system performance monitoring
        $performance = $this->app->make('App\Interfaces\Services\AlertEnhancementServiceInterface')
            ->monitorSystemPerformance($metrics);
        
        // Admin should receive system performance alert
        Notification::assertSentTo(
            $admin,
            SystemPerformanceAlert::class,
            function ($notification, $channels) {
                return $notification->performance->cpu_usage === 95;
            }
        );
    }
}
