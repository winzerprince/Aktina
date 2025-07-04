<?php

namespace Tests\Feature;

use App\Models\InventoryAlert;
use App\Models\Order;
use App\Models\SystemPerformance;
use App\Models\User;
use App\Notifications\LowStockAlert;
use App\Notifications\OrderApprovalRequest;
use App\Notifications\SystemPerformanceAlert;
use App\Services\AlertEnhancementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AlertEnhancementServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    
    protected AlertEnhancementService $service;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(AlertEnhancementService::class);
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_inventory_alert_emails()
    {
        Notification::fake();
        
        $user = User::factory()->create(['role' => 'admin']);
        $alert = InventoryAlert::factory()->create([
            'type' => 'product',
            'product_id' => 1,
            'current_level' => 3,
            'threshold_level' => 5,
            'severity' => 'critical'
        ]);
        
        $this->service->sendInventoryAlertEmails($alert);
        
        Notification::assertSentTo(
            [$user], LowStockAlert::class
        );
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_order_approval_notifications()
    {
        Notification::fake();
        
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create([
            'status' => 'pending',
            'total_amount' => 5000
        ]);
        
        $this->service->sendOrderApprovalNotification($order);
        
        Notification::assertSentTo(
            [$admin], OrderApprovalRequest::class
        );
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_monitors_system_performance_and_sends_alerts_when_thresholds_exceeded()
    {
        Notification::fake();
        
        $admin = User::factory()->create(['role' => 'admin']);
        
        $metrics = [
            'cpu_usage' => 90,      // Over threshold
            'memory_usage' => 50,    // Under threshold
            'disk_usage' => 95,      // Over threshold
            'response_time' => 1500  // Under threshold
        ];
        
        $performance = $this->service->monitorSystemPerformance($metrics);
        
        // Check that a SystemPerformance record was created
        $this->assertInstanceOf(SystemPerformance::class, $performance);
        $this->assertEquals(90, $performance->cpu_usage);
        $this->assertEquals(95, $performance->disk_usage);
        
        // Check that alerts were triggered (since CPU and disk are over threshold)
        Notification::assertSentTo(
            [$admin], SystemPerformanceAlert::class
        );
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_gets_and_sets_alert_thresholds()
    {
        Cache::flush();
        
        // Test setting a threshold
        $result = $this->service->setAlertThreshold('products.critical', 3);
        $this->assertTrue($result);
        
        // Test getting the threshold
        $value = $this->service->getAlertThreshold('products.critical');
        $this->assertEquals(3, $value);
        
        // Test default value
        $value = $this->service->getAlertThreshold('nonexistent', 'default');
        $this->assertEquals('default', $value);
        
        // Test getting all thresholds
        $thresholds = $this->service->getAllAlertThresholds();
        $this->assertIsArray($thresholds);
        $this->assertArrayHasKey('products', $thresholds);
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_selects_appropriate_order_approver_based_on_order_value()
    {
        // Create users with different roles
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'production_manager']);
        
        // Test high-value order (should select admin)
        $highValueOrder = Order::factory()->create(['total_amount' => 5000]);
        $approver = $this->service->selectOrderApprover($highValueOrder);
        $this->assertEquals($admin->id, $approver->id);
        
        // Test standard order (should select any approver)
        $standardOrder = Order::factory()->create(['total_amount' => 500]);
        $approver = $this->service->selectOrderApprover($standardOrder);
        $this->assertContains($approver->role, ['admin', 'production_manager']);
    }
}
