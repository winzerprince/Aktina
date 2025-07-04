<?php

namespace App\Livewire\ProductionManager;

use App\Services\ProductionEfficiencyService;
use App\Services\InventoryService;
use App\Services\OrderService;
use Livewire\Component;
use Livewire\Attributes\On;

class ProductionDashboard extends Component
{
    public $efficiencyMetrics = [];
    public $fulfillmentStats = [];
    public $resourceConsumption = [];
    public $inventoryOverview = [];
    public $recentOrders = [];
    public $alerts = [];
    public $selectedTimeframe = '7d';
    public $selectedWarehouse = 'all';
    public $warehouses = [];

    public $timeframes = [
        '24h' => 'Last 24 Hours',
        '7d' => 'Last 7 Days',
        '30d' => 'Last 30 Days',
        '90d' => 'Last 3 Months'
    ];

    protected $productionEfficiencyService;
    protected $inventoryService;
    protected $orderService;

    public function boot(
        ProductionEfficiencyService $productionEfficiencyService,
        InventoryService $inventoryService,
        OrderService $orderService
    ) {
        $this->productionEfficiencyService = $productionEfficiencyService;
        $this->inventoryService = $inventoryService;
        $this->orderService = $orderService;
    }

    public function mount()
    {
        $this->loadWarehouses();
        $this->loadDashboardData();
    }

    public function updatedSelectedTimeframe()
    {
        $this->loadDashboardData();
    }

    public function updatedSelectedWarehouse()
    {
        $this->loadDashboardData();
    }

    public function loadWarehouses()
    {
        $this->warehouses = $this->inventoryService->getWarehouses();
    }

    public function loadDashboardData()
    {
        $this->loadEfficiencyMetrics();
        $this->loadFulfillmentStats();
        $this->loadResourceConsumption();
        $this->loadInventoryOverview();
        $this->loadRecentOrders();
        $this->loadAlerts();
    }

    private function loadEfficiencyMetrics()
    {
        $timeframe = $this->getTimeframeDate();
        
        // Get efficiency trend data for charts
        $trendData = $this->productionEfficiencyService->getEfficiencyTrend($timeframe);
        
        // Calculate trend percentage (current vs previous period)
        $trendPercentage = 0;
        if (count($trendData) >= 2) {
            $latestEfficiency = end($trendData)['efficiency'];
            $previousEfficiency = prev($trendData)['efficiency'];
            if ($previousEfficiency > 0) {
                $trendPercentage = (($latestEfficiency - $previousEfficiency) / $previousEfficiency) * 100;
            }
        }
        
        $this->efficiencyMetrics = [
            'overall_efficiency' => $this->productionEfficiencyService->getOverallEfficiency($timeframe),
            'production_rate' => $this->productionEfficiencyService->getProductionRate($timeframe),
            'quality_score' => $this->productionEfficiencyService->getQualityScore($timeframe),
            'downtime_hours' => $this->productionEfficiencyService->getDowntimeHours($timeframe),
            'throughput' => $this->productionEfficiencyService->getThroughput($timeframe),
            'efficiency_trend' => $trendPercentage,
            'efficiency_trend_data' => $trendData, // Keep the full data for charts
        ];
    }

    private function loadFulfillmentStats()
    {
        $timeframe = $this->getTimeframeDate();
        
        $this->fulfillmentStats = [
            'fulfillment_rate' => $this->orderService->getFulfillmentRate($timeframe),
            'avg_fulfillment_time' => $this->orderService->getAverageFulfillmentTime($timeframe),
            'on_time_delivery' => $this->orderService->getOnTimeDeliveryRate($timeframe),
            'pending_orders' => $this->orderService->getPendingOrdersCount(),
            'completed_today' => $this->orderService->getCompletedTodayCount(),
            'fulfillment_trend' => $this->orderService->getFulfillmentTrend($timeframe),
        ];
    }

    private function loadResourceConsumption()
    {
        $timeframe = $this->getTimeframeDate();
        $warehouseId = $this->selectedWarehouse !== 'all' ? $this->selectedWarehouse : null;
        
        $this->resourceConsumption = [
            'material_usage' => $this->inventoryService->getMaterialUsage($timeframe, $warehouseId),
            'labor_hours' => $this->productionEfficiencyService->getLaborHours($timeframe),
            'energy_consumption' => $this->productionEfficiencyService->getEnergyConsumption($timeframe),
            'cost_breakdown' => $this->productionEfficiencyService->getCostBreakdown($timeframe),
            'utilization_rate' => $this->productionEfficiencyService->getUtilizationRate($timeframe),
            'waste_percentage' => $this->productionEfficiencyService->getWastePercentage($timeframe),
        ];
    }

    private function loadInventoryOverview()
    {
        $warehouseId = $this->selectedWarehouse !== 'all' ? $this->selectedWarehouse : null;
        
        $this->inventoryOverview = [
            'total_items' => $this->inventoryService->getTotalItems($warehouseId),
            'low_stock_items' => $this->inventoryService->getLowStockCount($warehouseId),
            'out_of_stock' => $this->inventoryService->getOutOfStockCount($warehouseId),
            'warehouse_capacity' => $this->inventoryService->getWarehouseCapacity($warehouseId),
            'capacity_utilization' => $this->inventoryService->getCapacityUtilization($warehouseId),
            'recent_movements' => $this->inventoryService->getRecentMovements($warehouseId, 10),
        ];
    }

    private function loadRecentOrders()
    {
        $this->recentOrders = $this->orderService->getRecentOrdersForProduction(20);
    }

    private function loadAlerts()
    {
        $this->alerts = [
            'production_alerts' => $this->productionEfficiencyService->getProductionAlerts(),
            'inventory_alerts' => $this->inventoryService->getInventoryAlerts(),
            'quality_alerts' => $this->productionEfficiencyService->getQualityAlerts(),
            'maintenance_alerts' => $this->productionEfficiencyService->getMaintenanceAlerts(),
        ];
    }

    private function getTimeframeDate()
    {
        return match($this->selectedTimeframe) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => now()->subDays(7),
        };
    }

    public function acknowledgeAlert($alertId, $type)
    {
        try {
            if ($type === 'production') {
                $this->productionEfficiencyService->acknowledgeAlert($alertId);
            } elseif ($type === 'inventory') {
                $this->inventoryService->acknowledgeAlert($alertId);
            }
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Alert acknowledged successfully'
            ]);

            $this->loadAlerts();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to acknowledge alert: ' . $e->getMessage()
            ]);
        }
    }

    public function resolveAlert($alertId, $type)
    {
        try {
            if ($type === 'production') {
                $this->productionEfficiencyService->resolveAlert($alertId);
            } elseif ($type === 'inventory') {
                $this->inventoryService->resolveAlert($alertId);
            }
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Alert resolved successfully'
            ]);

            $this->loadAlerts();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to resolve alert: ' . $e->getMessage()
            ]);
        }
    }

    #[On('refresh-dashboard')]
    public function refreshDashboard()
    {
        $this->loadDashboardData();
    }

    public function exportReport()
    {
        try {
            $filters = [
                'timeframe' => $this->selectedTimeframe,
                'warehouse' => $this->selectedWarehouse,
                'type' => 'production_report'
            ];

            $this->productionEfficiencyService->exportReport($filters);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Export started. Download will begin shortly.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.production-manager.production-dashboard');
    }
}
