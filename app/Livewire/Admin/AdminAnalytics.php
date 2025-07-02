<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\AdminAnalyticsService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdminAnalytics extends Component
{
    public $dateRange = '30_days';
    public $selectedMetric = 'revenue';
    public $chartType = 'line';
    
    // Analytics data properties
    public $revenueMetrics = [];
    public $orderMetrics = [];
    public $userMetrics = [];
    public $inventoryMetrics = [];
    public $performanceMetrics = [];
    
    // Chart data
    public $chartData = [];
    public $comparisonData = [];
    
    // Export flags
    public $isExporting = false;
    
    protected $listeners = [
        'refreshAnalytics' => 'loadAnalytics',
        'exportAnalytics' => 'exportData'
    ];

    public function mount()
    {
        $this->loadAnalytics();
    }
    
    public function updatedDateRange()
    {
        $this->loadAnalytics();
    }
    
    public function updatedSelectedMetric()
    {
        $this->loadChartData();
    }
    
    public function updatedChartType()
    {
        $this->dispatch('chartTypeChanged', $this->chartType);
    }

    public function loadAnalytics()
    {
        $cacheKey = "admin_analytics_{$this->dateRange}";
        
        $analytics = Cache::remember($cacheKey, 300, function () {
            $service = app(AdminAnalyticsService::class);
            return $service->getComprehensiveAnalytics($this->dateRange);
        });
        
        $this->revenueMetrics = $analytics['revenue'];
        $this->orderMetrics = $analytics['orders'];
        $this->userMetrics = $analytics['users'];
        $this->inventoryMetrics = $analytics['inventory'];
        $this->performanceMetrics = $analytics['performance'];
        
        $this->loadChartData();
        $this->dispatch('analyticsRefreshed');
    }
    
    public function loadChartData()
    {
        $cacheKey = "chart_data_{$this->selectedMetric}_{$this->dateRange}";
        
        $this->chartData = Cache::remember($cacheKey, 300, function () {
            $service = app(AdminAnalyticsService::class);
            return $service->getChartData($this->selectedMetric, $this->dateRange);
        });
        
        $this->comparisonData = Cache::remember($cacheKey . '_comparison', 300, function () {
            $service = app(AdminAnalyticsService::class);
            return $service->getComparisonData($this->selectedMetric, $this->dateRange);
        });
        
        $this->dispatch('chartDataUpdated', [
            'data' => $this->chartData,
            'comparison' => $this->comparisonData,
            'metric' => $this->selectedMetric,
            'type' => $this->chartType
        ]);
    }
    
    public function exportData($format = 'csv')
    {
        $this->isExporting = true;
        
        try {
            $service = app(AdminAnalyticsService::class);
            $fileName = $service->exportAnalytics($this->dateRange, $format);
            
            $this->dispatch('downloadFile', $fileName);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Analytics data exported successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to export data: ' . $e->getMessage()
            ]);
        } finally {
            $this->isExporting = false;
        }
    }
    
    public function generateReport($type = 'comprehensive')
    {
        try {
            $service = app(AdminAnalyticsService::class);
            $reportPath = $service->generateReport($type, $this->dateRange);
            
            $this->dispatch('downloadFile', $reportPath);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Report generated successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ]);
        }
    }
    
    public function refreshCache()
    {
        // Clear relevant cache keys
        $patterns = [
            "admin_analytics_{$this->dateRange}",
            "chart_data_{$this->selectedMetric}_{$this->dateRange}",
            "chart_data_{$this->selectedMetric}_{$this->dateRange}_comparison"
        ];
        
        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
        
        $this->loadAnalytics();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Analytics data refreshed!'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.admin-analytics');
    }
}
