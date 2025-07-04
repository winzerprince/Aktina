<?php

namespace Tests\Feature\Livewire\Admin;

use App\Interfaces\Services\AlertEnhancementServiceInterface;
use App\Livewire\Admin\AlertThresholdManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class AlertThresholdManagerTest extends TestCase
{
    use RefreshDatabase;
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_alert_thresholds_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this
            ->actingAs($admin)
            ->get(route('admin.alert-thresholds'));
            
        $response->assertStatus(200)
            ->assertSeeLivewire(AlertThresholdManager::class);
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_view_alert_thresholds_page()
    {
        $vendor = User::factory()->create(['role' => 'vendor']);
        
        $response = $this
            ->actingAs($vendor)
            ->get(route('admin.alert-thresholds'));
            
        $response->assertStatus(403);
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_loads_thresholds_from_service()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Mock the service to return specific thresholds
        $mockService = $this->mock(AlertEnhancementServiceInterface::class);
        $mockService->shouldReceive('getAllAlertThresholds')
            ->once()
            ->andReturn([
                'products' => [
                    'critical' => 5,
                    'warning' => 15
                ],
                'performance' => [
                    'cpu_usage' => 80
                ]
            ]);
        
        Livewire::actingAs($admin)
            ->test(AlertThresholdManager::class)
            ->assertSet('thresholds.products.critical', 5)
            ->assertSet('thresholds.products.warning', 15)
            ->assertSet('thresholds.performance.cpu_usage', 80);
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_threshold_value()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Mock the service
        $mockService = $this->mock(AlertEnhancementServiceInterface::class);
        $mockService->shouldReceive('getAllAlertThresholds')
            ->andReturn([
                'products' => [
                    'critical' => 5,
                    'warning' => 15
                ]
            ]);
        
        $mockService->shouldReceive('setAlertThreshold')
            ->with('products.critical', 3)
            ->once()
            ->andReturn(true);
        
        Livewire::actingAs($admin)
            ->test(AlertThresholdManager::class)
            ->set('thresholds.products.critical', 3)
            ->call('updateThreshold', 'products.critical')
            ->assertEmitted('thresholdUpdated')
            ->assertDispatched('notify');
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_threshold_values()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Mock the service
        $mockService = $this->mock(AlertEnhancementServiceInterface::class);
        $mockService->shouldReceive('getAllAlertThresholds')
            ->andReturn([
                'products' => [
                    'critical' => 5,
                    'warning' => 15
                ]
            ]);
        
        // Test with invalid input
        Livewire::actingAs($admin)
            ->test(AlertThresholdManager::class)
            ->set('thresholds.products.critical', -1)
            ->call('updateThreshold', 'products.critical')
            ->assertHasErrors(['thresholds.products.critical']);
    }
}
