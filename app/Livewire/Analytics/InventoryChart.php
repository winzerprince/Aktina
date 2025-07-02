<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use App\Interfaces\Services\InventoryServiceInterface;
use App\Interfaces\Services\WarehouseServiceInterface;
use App\Models\Warehouse;

class InventoryChart extends Component
{
    public $selectedWarehouse = 'all';
    public $chartView = 'stock_levels'; // stock_levels, turnover, alerts
    public $stockData = [];
    public $turnoverData = [];
    public $alertsData = [];
    public $warehouses = [];
    public $totalStock = 0;
    public $lowStockItems = 0;
    public $averageTurnover = 0;
    public $loading = false;

    protected $inventoryService;
    protected $warehouseService;

    public function boot(
        InventoryServiceInterface $inventoryService,
        WarehouseServiceInterface $warehouseService
    ) {
        $this->inventoryService = $inventoryService;
        $this->warehouseService = $warehouseService;
    }

    public function mount()
    {
        $this->warehouses = Warehouse::all();
        $this->loadInventoryData();
    }

    public function updatedSelectedWarehouse()
    {
        $this->loadInventoryData();
    }

    public function updatedChartView()
    {
        $this->loadInventoryData();
    }

    public function loadInventoryData()
    {
        $this->loading = true;

        try {
            $warehouseId = $this->selectedWarehouse !== 'all' ? $this->selectedWarehouse : null;

            switch ($this->chartView) {
                case 'stock_levels':
                    $this->loadStockLevelsData($warehouseId);
                    break;
                case 'turnover':
                    $this->loadTurnoverData($warehouseId);
                    break;
                case 'alerts':
                    $this->loadAlertsData($warehouseId);
                    break;
            }

            $this->loadSummaryMetrics($warehouseId);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load inventory data: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    private function loadStockLevelsData($warehouseId)
    {
        $this->stockData = $this->inventoryService->getStockLevelsChart($warehouseId);
    }

    private function loadTurnoverData($warehouseId)
    {
        $this->turnoverData = $this->inventoryService->getInventoryTurnoverChart([
            'warehouse_id' => $warehouseId,
            'period' => 'last_12_months'
        ]);
    }

    private function loadAlertsData($warehouseId)
    {
        $this->alertsData = $this->inventoryService->getInventoryAlertsChart($warehouseId);
    }

    private function loadSummaryMetrics($warehouseId)
    {
        $summary = $this->inventoryService->getInventorySummary($warehouseId);
        
        $this->totalStock = $summary['total_stock'] ?? 0;
        $this->lowStockItems = $summary['low_stock_items'] ?? 0;
        $this->averageTurnover = $summary['average_turnover'] ?? 0;
    }

    public function refreshData()
    {
        $this->loadInventoryData();
        $this->dispatch('dataRefreshed');
    }

    public function render()
    {
        return view('livewire.analytics.inventory-chart');
    }
}
