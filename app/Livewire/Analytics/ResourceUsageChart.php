<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use App\Interfaces\Services\WarehouseServiceInterface;
use App\Interfaces\Services\AnalyticsServiceInterface;
use App\Models\Warehouse;

class ResourceUsageChart extends Component
{
    public $viewType = 'warehouse'; // warehouse, production, capacity
    public $selectedWarehouse = 'all';
    public $timeRange = '30d'; // 7d, 30d, 90d
    public $chartData = [];
    public $warehouses = [];
    
    // Summary metrics
    public $totalCapacity = 0;
    public $usedCapacity = 0;
    public $utilizationPercentage = 0;
    public $efficiencyScore = 0;
    public $topPerformers = [];
    public $bottomPerformers = [];
    public $loading = false;

    protected $warehouseService;
    protected $analyticsService;

    public function boot(
        WarehouseServiceInterface $warehouseService,
        AnalyticsServiceInterface $analyticsService
    ) {
        $this->warehouseService = $warehouseService;
        $this->analyticsService = $analyticsService;
    }

    public function mount()
    {
        $this->warehouses = Warehouse::all();
        $this->loadResourceData();
    }

    public function updatedViewType()
    {
        $this->loadResourceData();
    }

    public function updatedSelectedWarehouse()
    {
        $this->loadResourceData();
    }

    public function updatedTimeRange()
    {
        $this->loadResourceData();
    }

    public function loadResourceData()
    {
        $this->loading = true;

        try {
            $warehouseId = $this->selectedWarehouse !== 'all' ? $this->selectedWarehouse : null;

            switch ($this->viewType) {
                case 'warehouse':
                    $this->loadWarehouseUsage($warehouseId);
                    break;
                case 'production':
                    $this->loadProductionUsage($warehouseId);
                    break;
                case 'capacity':
                    $this->loadCapacityUsage($warehouseId);
                    break;
            }

            $this->loadSummaryMetrics($warehouseId);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load resource data: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    private function loadWarehouseUsage($warehouseId)
    {
        $this->chartData = $this->warehouseService->getWarehouseUsageChart([
            'warehouse_id' => $warehouseId,
            'time_range' => $this->timeRange
        ]);
    }

    private function loadProductionUsage($warehouseId)
    {
        $this->chartData = $this->analyticsService->getProductionUsageChart([
            'warehouse_id' => $warehouseId,
            'time_range' => $this->timeRange
        ]);
    }

    private function loadCapacityUsage($warehouseId)
    {
        $this->chartData = $this->warehouseService->getCapacityUsageChart([
            'warehouse_id' => $warehouseId,
            'time_range' => $this->timeRange
        ]);
    }

    private function loadSummaryMetrics($warehouseId)
    {
        // Get capacity summary
        $capacitySummary = $this->warehouseService->getCapacitySummary($warehouseId);
        
        $this->totalCapacity = $capacitySummary['total_capacity'] ?? 0;
        $this->usedCapacity = $capacitySummary['used_capacity'] ?? 0;
        $this->utilizationPercentage = $capacitySummary['utilization_percentage'] ?? 0;

        // Get efficiency metrics
        $efficiencyMetrics = $this->analyticsService->getResourceEfficiency([
            'warehouse_id' => $warehouseId,
            'time_range' => $this->timeRange
        ]);
        
        $this->efficiencyScore = $efficiencyMetrics['efficiency_score'] ?? 0;
        $this->topPerformers = $efficiencyMetrics['top_performers'] ?? [];
        $this->bottomPerformers = $efficiencyMetrics['bottom_performers'] ?? [];
    }

    public function exportResourceReport()
    {
        try {
            $warehouseId = $this->selectedWarehouse !== 'all' ? $this->selectedWarehouse : null;
            
            // This would typically generate and download a report
            session()->flash('success', 'Resource usage report export started. You will receive the file shortly.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export report: ' . $e->getMessage());
        }
    }

    public function refreshData()
    {
        $this->loadResourceData();
        $this->dispatch('resourceDataRefreshed');
    }

    public function render()
    {
        return view('livewire.analytics.resource-usage-chart');
    }
}
