<?php

namespace App\Livewire\Supplier;

use App\Services\SupplierService;
use Livewire\Component;

class SupplierDashboard extends Component
{
    public $timeframe = '30';
    public $refreshInterval = 30000; // 30 seconds

    public function mount()
    {
        // Initialize component
    }

    public function updateTimeframe($timeframe)
    {
        $this->timeframe = $timeframe;
    }

    public function refresh()
    {
        // Force refresh data
        $this->render();
    }

    public function render()
    {
        $supplierService = app(SupplierService::class);

        $stats = $supplierService->getSupplierStats();
        $recentOrders = $supplierService->getRecentOrders();
        $orderTrends = $supplierService->getOrderTrends((int)$this->timeframe);
        $resourceMetrics = $supplierService->getResourceSupplyMetrics();
        $topResources = $supplierService->getTopRequestedResources();
        $performanceMetrics = $supplierService->getSupplyPerformanceMetrics();
        $ordersByStatus = $supplierService->getOrdersByStatus();
        $resourceCategories = $supplierService->getResourceCategories();

        return view('livewire.supplier.supplier-dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'orderTrends' => $orderTrends,
            'resourceMetrics' => $resourceMetrics,
            'topResources' => $topResources,
            'performanceMetrics' => $performanceMetrics,
            'ordersByStatus' => $ordersByStatus,
            'resourceCategories' => $resourceCategories,
        ]);
    }
}
