<?php

namespace Tests\Feature\Admin;

use App\Models\SystemPerformance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AlertDashboardTest extends TestCase
{
    use RefreshDatabase;
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_access_system_performance_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create some system performance records
        SystemPerformance::factory()->count(3)->create();
        
        $response = $this
            ->actingAs($admin)
            ->get(route('admin.system.performance'));
        
        $response->assertStatus(200)
            ->assertViewIs('admin.system-performance');
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_access_alert_thresholds_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this
            ->actingAs($admin)
            ->get(route('admin.alert-thresholds'));
        
        $response->assertStatus(200)
            ->assertViewIs('admin.alert-thresholds');
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_access_system_performance_dashboard()
    {
        $vendor = User::factory()->create(['role' => 'vendor']);
        
        $response = $this
            ->actingAs($vendor)
            ->get(route('admin.system.performance'));
        
        $response->assertStatus(403);
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_access_alert_thresholds_page()
    {
        $vendor = User::factory()->create(['role' => 'vendor']);
        
        $response = $this
            ->actingAs($vendor)
            ->get(route('admin.alert-thresholds'));
        
        $response->assertStatus(403);
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function system_performance_dashboard_shows_correct_elements()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create system performance records with issues
        SystemPerformance::factory()->withIssues()->create([
            'cpu_usage' => 90,
            'memory_usage' => 95,
            'created_at' => now()
        ]);
        
        $response = $this
            ->actingAs($admin)
            ->get(route('admin.system.performance'));
        
        $response->assertStatus(200)
            ->assertSee('System Performance Monitoring')
            ->assertSee('CPU Usage')
            ->assertSee('Memory Usage')
            ->assertSee('Disk Usage')
            ->assertSee('System Performance History')
            ->assertSee('Recent System Alerts');
    }
}
