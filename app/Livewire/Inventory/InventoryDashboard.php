<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Interfaces\Services\InventoryServiceInterface;
use App\Interfaces\Services\WarehouseServiceInterface;
use App\Interfaces\Services\AlertServiceInterface;

class InventoryDashboard extends Component
{
    public $totalResources = 0;
    public $lowStockCount = 0;
    public $warehouseUtilization = 0;
    public $stockMovements = [];
    public $selectedWarehouse = 'all';
    public $warehouses = [];
    public $stockLevels = [];
    public $refreshInterval = 30000; // 30 seconds

    protected $inventoryService;
    protected $warehouseService;
    protected $alertService;

    public function boot(
        InventoryServiceInterface $inventoryService,
        WarehouseServiceInterface $warehouseService,
        AlertServiceInterface $alertService
    ) {
        $this->inventoryService = $inventoryService;
        $this->warehouseService = $warehouseService;
        $this->alertService = $alertService;
    }

    public function mount()
    {
        $this->loadDashboardData();
        $this->loadWarehouses();
    }

    public function updatedSelectedWarehouse()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        try {
            // Get real-time stock levels
            $stockData = $this->inventoryService->getRealTimeStockLevels($this->selectedWarehouse);
            $this->stockLevels = $stockData['stock_levels'] ?? [];
            $this->totalResources = $stockData['total_resources'] ?? 0;
            
            // Get low stock alerts count
            $this->lowStockCount = $this->alertService->getLowStockAlertsCount($this->selectedWarehouse);
            
            // Get warehouse utilization
            $this->warehouseUtilization = $this->warehouseService->getUtilizationPercentage($this->selectedWarehouse);
            
            // Get recent stock movements
            $this->stockMovements = $this->inventoryService->getRecentMovements(10, $this->selectedWarehouse);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load inventory data: ' . $e->getMessage());
        }
    }

    public function loadWarehouses()
    {
        try {
            $this->warehouses = $this->warehouseService->getAllWarehouses();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load warehouses: ' . $e->getMessage());
        }
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->dispatch('refreshed');
    }

    public function render()
    {
        return view('livewire.inventory.inventory-dashboard');
    }
}
