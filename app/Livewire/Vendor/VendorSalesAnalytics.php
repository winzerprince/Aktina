<?php

namespace App\Livewire\Vendor;

use App\Services\VendorSalesService;
use Livewire\Component;

class VendorSalesAnalytics extends Component
{
    public $selectedPeriod = '30d';
    public $selectedMetric = 'revenue';
    public $salesData = [];
    public $revenueByProduct = [];
    public $performanceMetrics = [];
    public $goalAmount = 50000;

    public $periods = [
        '7d' => 'Last 7 Days',
        '30d' => 'Last 30 Days', 
        '90d' => 'Last 3 Months',
        '1y' => 'Last Year'
    ];

    public $metrics = [
        'revenue' => 'Revenue Analysis',
        'orders' => 'Order Analysis',
        'products' => 'Product Performance',
        'retailers' => 'Retailer Performance'
    ];

    protected $vendorSalesService;

    public function boot(VendorSalesService $vendorSalesService)
    {
        $this->vendorSalesService = $vendorSalesService;
    }

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function updatedSelectedPeriod()
    {
        $this->loadAnalytics();
    }

    public function updatedSelectedMetric()
    {
        $this->loadAnalytics();
    }

    public function updatedGoalAmount()
    {
        $this->loadGoalProgress();
    }

    public function loadAnalytics()
    {
        $vendorId = auth()->id();
        
        $this->salesData = $this->vendorSalesService->getSalesMetrics($vendorId, $this->selectedPeriod);
        $this->revenueByProduct = $this->vendorSalesService->getRevenueByProduct($vendorId, $this->selectedPeriod);
        $this->performanceMetrics = $this->vendorSalesService->getRetailerPerformanceMetrics($vendorId, $this->selectedPeriod);
        $this->loadGoalProgress();
    }

    private function loadGoalProgress()
    {
        $vendorId = auth()->id();
        $this->salesData['goal_progress'] = $this->vendorSalesService->getSalesGoalProgress(
            $vendorId, 
            $this->goalAmount, 
            $this->selectedPeriod
        );
    }

    public function exportDetailedReport()
    {
        try {
            $vendorId = auth()->id();
            
            $detailedData = [
                'period' => $this->selectedPeriod,
                'metric_focus' => $this->selectedMetric,
                'sales_data' => $this->salesData,
                'revenue_by_product' => $this->revenueByProduct,
                'performance_metrics' => $this->performanceMetrics,
                'sales_trend' => $this->vendorSalesService->getSalesTrend($vendorId, $this->selectedPeriod),
                'top_retailers' => $this->vendorSalesService->getTopRetailers($vendorId, $this->selectedPeriod, 20),
                'generated_at' => now()->toISOString(),
            ];

            $filename = 'detailed_sales_analytics_' . date('Y-m-d_H-i-s') . '.json';
            
            $this->dispatch('download-file', [
                'filename' => $filename,
                'content' => json_encode($detailedData, JSON_PRETTY_PRINT),
                'type' => 'application/json'
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Detailed analytics exported successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to export analytics: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.vendor.vendor-sales-analytics');
    }
}
