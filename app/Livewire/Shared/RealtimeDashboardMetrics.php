<?php

namespace App\Livewire\Shared;

use App\Services\RealtimeDataService;
use Livewire\Component;

class RealtimeDashboardMetrics extends Component
{
    public $metrics = [];
    public $inventoryData = [];
    public $orderData = [];
    public $productionData = [];
    public $refreshInterval = 30000; // 30 seconds
    public $lastUpdated = null;

    public function mount()
    {
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        $realtimeService = app(RealtimeDataService::class);
        
        $this->metrics = $realtimeService->getRealtimeDashboardMetrics();
        $this->inventoryData = $realtimeService->getRealtimeInventoryData();
        $this->orderData = $realtimeService->getRealtimeOrderData();
        $this->productionData = $realtimeService->getRealtimeProductionData();
        $this->lastUpdated = now()->format('H:i:s');
    }

    public function refresh()
    {
        $this->loadMetrics();
        $this->dispatch('metricsUpdated', $this->metrics);
    }

    public function render()
    {
        return view('livewire.shared.realtime-dashboard-metrics');
    }
}
