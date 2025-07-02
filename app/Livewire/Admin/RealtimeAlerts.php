<?php

namespace App\Livewire\Admin;

use App\Services\AlertService;
use App\Services\SystemHealthService;
use Livewire\Component;
use Livewire\Attributes\On;

class RealtimeAlerts extends Component
{
    public $criticalAlerts = [];
    public $systemAlerts = [];
    public $inventoryAlerts = [];
    public $unreadNotifications = 0;
    public $showNotifications = false;
    public $autoRefresh = true;
    public $lastRefresh;

    protected $alertService;
    protected $systemHealthService;

    public function boot(AlertService $alertService, SystemHealthService $systemHealthService)
    {
        $this->alertService = $alertService;
        $this->systemHealthService = $systemHealthService;
    }

    public function mount()
    {
        $this->loadAlerts();
        $this->lastRefresh = now()->format('H:i:s');
    }

    public function loadAlerts()
    {
        // Load critical inventory alerts
        $this->criticalAlerts = $this->alertService->getCriticalAlerts()
            ->take(5)
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'type' => 'inventory',
                    'title' => 'Critical Stock Alert',
                    'message' => "Low stock: {$alert->resource->name} ({$alert->current_value} remaining)",
                    'severity' => 'critical',
                    'timestamp' => $alert->created_at->diffForHumans(),
                    'icon' => 'exclamation-triangle',
                    'color' => 'red'
                ];
            })
            ->toArray();

        // Load system health alerts
        $systemHealth = $this->systemHealthService->getSystemHealth();
        $this->systemAlerts = collect($systemHealth['services'])
            ->filter(fn($service) => $service['status'] !== 'healthy')
            ->map(function ($service, $name) {
                return [
                    'id' => 'system_' . $name,
                    'type' => 'system',
                    'title' => 'System Alert',
                    'message' => "{$name} service is {$service['status']}",
                    'severity' => $service['status'] === 'down' ? 'critical' : 'warning',
                    'timestamp' => 'Just now',
                    'icon' => $service['status'] === 'down' ? 'times-circle' : 'exclamation-circle',
                    'color' => $service['status'] === 'down' ? 'red' : 'yellow'
                ];
            })
            ->values()
            ->toArray();

        // Load inventory alerts
        $this->inventoryAlerts = $this->alertService->getActiveAlerts()
            ->where('alert_type', '!=', 'critical')
            ->take(5)
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'type' => 'inventory',
                    'title' => ucfirst(str_replace('_', ' ', $alert->alert_type)),
                    'message' => "{$alert->resource->name}: {$alert->current_value}/{$alert->threshold_value}",
                    'severity' => 'warning',
                    'timestamp' => $alert->created_at->diffForHumans(),
                    'icon' => 'warehouse',
                    'color' => 'orange'
                ];
            })
            ->toArray();

        $this->unreadNotifications = count($this->criticalAlerts) + count($this->systemAlerts) + count($this->inventoryAlerts);
        $this->lastRefresh = now()->format('H:i:s');
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function markAsRead($alertId, $type)
    {
        if ($type === 'inventory') {
            $this->alertService->markAsRead($alertId);
        }
        
        $this->loadAlerts();
        $this->dispatch('alert-read', ['id' => $alertId, 'type' => $type]);
    }

    public function dismissAlert($alertId, $type)
    {
        if ($type === 'inventory') {
            $this->alertService->dismissAlert($alertId);
        }
        
        $this->loadAlerts();
        $this->dispatch('alert-dismissed', ['id' => $alertId, 'type' => $type]);
    }

    public function resolveAlert($alertId, $type)
    {
        if ($type === 'inventory') {
            $this->alertService->resolveAlert($alertId);
        }
        
        $this->loadAlerts();
        $this->dispatch('alert-resolved', ['id' => $alertId, 'type' => $type]);
    }

    #[On('refresh-alerts')]
    public function refreshAlerts()
    {
        $this->loadAlerts();
    }

    public function render()
    {
        if ($this->autoRefresh) {
            $this->loadAlerts();
        }
        
        return view('livewire.admin.realtime-alerts');
    }
}
