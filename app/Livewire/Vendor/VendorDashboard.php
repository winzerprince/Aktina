<?php

namespace App\Livewire\Vendor;

use App\Services\VendorSalesService;
use App\Services\InventoryService;
use App\Services\OrderService;
use Livewire\Component;
use Livewire\Attributes\On;

class VendorDashboard extends Component
{
    public $salesMetrics = [];
    public $salesTrend = [];
    public $topRetailers = [];
    public $inventoryOverview = [];
    public $recentOrders = [];
    public $retailerMetrics = [];
    public $selectedTimeframe = '30d';
    public $salesGoal = 50000; // Default sales goal

    public $timeframes = [
        '7d' => 'Last 7 Days',
        '30d' => 'Last 30 Days', 
        '90d' => 'Last 3 Months',
        '1y' => 'Last Year'
    ];

    protected $vendorSalesService;
    protected $inventoryService;
    protected $orderService;

    public function boot(
        VendorSalesService $vendorSalesService,
        InventoryService $inventoryService,
        OrderService $orderService
    ) {
        $this->vendorSalesService = $vendorSalesService;
        $this->inventoryService = $inventoryService;
        $this->orderService = $orderService;
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function updatedSelectedTimeframe()
    {
        $this->loadDashboardData();
    }

    public function updatedSalesGoal()
    {
        $this->loadSalesGoalProgress();
    }

    public function loadDashboardData()
    {
        $vendorId = auth()->id();
        
        $this->loadSalesMetrics($vendorId);
        $this->loadSalesTrend($vendorId);
        $this->loadTopRetailers($vendorId);
        $this->loadInventoryOverview($vendorId);
        $this->loadRecentOrders($vendorId);
        $this->loadRetailerMetrics($vendorId);
    }

    private function loadSalesMetrics($vendorId)
    {
        $this->salesMetrics = $this->vendorSalesService->getSalesMetrics($vendorId, $this->selectedTimeframe);
    }

    private function loadSalesTrend($vendorId)
    {
        $this->salesTrend = $this->vendorSalesService->getSalesTrend($vendorId, $this->selectedTimeframe);
    }

    private function loadTopRetailers($vendorId)
    {
        $this->topRetailers = $this->vendorSalesService->getTopRetailers($vendorId, $this->selectedTimeframe, 10);
    }

    private function loadInventoryOverview($vendorId)
    {
        $this->inventoryOverview = [
            'total_products' => $this->inventoryService->getTotalProductsByVendor($vendorId),
            'low_stock_count' => $this->inventoryService->getLowStockCountByVendor($vendorId),
            'out_of_stock' => $this->inventoryService->getOutOfStockCountByVendor($vendorId),
            'inventory_value' => $this->inventoryService->getTotalInventoryValueByVendor($vendorId),
            'turnover_rate' => $this->inventoryService->getInventoryTurnoverRate($vendorId, $this->selectedTimeframe),
        ];
    }

    private function loadRecentOrders($vendorId)
    {
        $this->recentOrders = $this->orderService->getRecentOrdersByVendor($vendorId, 10);
    }

    private function loadRetailerMetrics($vendorId)
    {
        $this->retailerMetrics = $this->vendorSalesService->getRetailerPerformanceMetrics($vendorId, $this->selectedTimeframe);
    }

    private function loadSalesGoalProgress()
    {
        $vendorId = auth()->id();
        $this->salesMetrics['goal_progress'] = $this->vendorSalesService->getSalesGoalProgress(
            $vendorId, 
            $this->salesGoal, 
            $this->selectedTimeframe
        );
    }

    public function exportSalesReport()
    {
        try {
            $vendorId = auth()->id();
            
            $reportData = [
                'timeframe' => $this->selectedTimeframe,
                'sales_metrics' => $this->salesMetrics,
                'sales_trend' => $this->salesTrend,
                'top_retailers' => $this->topRetailers,
                'retailer_metrics' => $this->retailerMetrics,
                'inventory_overview' => $this->inventoryOverview,
                'generated_at' => now()->toISOString(),
            ];

            $filename = 'vendor_sales_report_' . date('Y-m-d_H-i-s') . '.json';
            
            $this->dispatch('download-file', [
                'filename' => $filename,
                'content' => json_encode($reportData, JSON_PRETTY_PRINT),
                'type' => 'application/json'
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Sales report exported successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to export report: ' . $e->getMessage()
            ]);
        }
    }

    public function contactRetailer($retailerId)
    {
        $this->dispatch('open-chat', ['userId' => $retailerId]);
    }

    #[On('order-updated')]
    public function refreshOrders()
    {
        $this->loadRecentOrders(auth()->id());
    }

    #[On('inventory-updated')]
    public function refreshInventory()
    {
        $this->loadInventoryOverview(auth()->id());
    }

    public function render()
    {
        return view('livewire.vendor.vendor-dashboard');
    }
}
