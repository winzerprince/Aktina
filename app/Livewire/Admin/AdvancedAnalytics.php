<?php

namespace App\Livewire\Admin;

use App\Services\AdvancedAnalyticsService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class AdvancedAnalytics extends Component
{
    public $timeRange = '30d';
    public $selectedMetrics = ['revenue', 'orders', 'users', 'inventory'];
    public $chartType = 'line';
    public $refreshInterval = 5000; // 5 seconds
    
    public function mount()
    {
        $this->dispatch('init-advanced-charts');
    }

    #[Computed]
    public function analyticsService()
    {
        return app(AdvancedAnalyticsService::class);
    }

    #[Computed]
    public function revenueData()
    {
        return $this->analyticsService->getRevenueAnalytics($this->timeRange);
    }

    #[Computed]
    public function orderTrendData()
    {
        return $this->analyticsService->getOrderTrends($this->timeRange);
    }

    #[Computed]
    public function userGrowthData()
    {
        return $this->analyticsService->getUserGrowthData($this->timeRange);
    }

    #[Computed]
    public function inventoryMetrics()
    {
        return $this->analyticsService->getInventoryMetrics();
    }

    #[Computed]
    public function performanceMetrics()
    {
        return $this->analyticsService->getPerformanceMetrics();
    }

    #[Computed]
    public function salesHeatmapData()
    {
        return $this->analyticsService->getSalesHeatmapData($this->timeRange);
    }

    #[Computed]
    public function topPerformingProducts()
    {
        return $this->analyticsService->getTopPerformingProducts($this->timeRange, 10);
    }

    #[Computed]
    public function customerSegmentation()
    {
        return $this->analyticsService->getCustomerSegmentation();
    }

    public function updateTimeRange($range)
    {
        $this->timeRange = $range;
        $this->dispatch('refresh-charts');
    }

    public function updateChartType($type)
    {
        $this->chartType = $type;
        $this->dispatch('chart-type-changed', type: $type);
    }

    public function toggleMetric($metric)
    {
        if (in_array($metric, $this->selectedMetrics)) {
            $this->selectedMetrics = array_diff($this->selectedMetrics, [$metric]);
        } else {
            $this->selectedMetrics[] = $metric;
        }
        $this->dispatch('metrics-updated', metrics: $this->selectedMetrics);
    }

    public function exportData($format = 'csv')
    {
        $data = $this->analyticsService->exportAnalyticsData($this->timeRange, $this->selectedMetrics);
        
        $this->dispatch('download-export', [
            'data' => $data,
            'format' => $format,
            'filename' => 'analytics_' . $this->timeRange . '_' . now()->format('Y-m-d')
        ]);
    }

    public function refreshData()
    {
        $this->dispatch('data-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.advanced-analytics');
    }
}
