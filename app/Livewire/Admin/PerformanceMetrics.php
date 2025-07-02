<?php

namespace App\Livewire\Admin;

use App\Services\PerformanceMetricsService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class PerformanceMetrics extends Component
{
    public $selectedPeriod = '24h';
    public $metricType = 'all';
    public $autoRefresh = true;
    public $refreshInterval = 10000; // 10 seconds
    
    public function mount()
    {
        $this->dispatch('init-performance-metrics');
    }

    #[Computed]
    public function performanceService()
    {
        return app(PerformanceMetricsService::class);
    }

    #[Computed]
    public function responseTimeMetrics()
    {
        return $this->performanceService->getResponseTimeMetrics($this->selectedPeriod);
    }

    #[Computed]
    public function throughputMetrics()
    {
        return $this->performanceService->getThroughputMetrics($this->selectedPeriod);
    }

    #[Computed]
    public function errorMetrics()
    {
        return $this->performanceService->getErrorMetrics($this->selectedPeriod);
    }

    #[Computed]
    public function resourceUtilization()
    {
        return $this->performanceService->getResourceUtilization();
    }

    #[Computed]
    public function applicationPerformance()
    {
        return $this->performanceService->getApplicationPerformance();
    }

    #[Computed]
    public function databasePerformance()
    {
        return $this->performanceService->getDatabasePerformance();
    }

    #[Computed]
    public function bottleneckAnalysis()
    {
        return $this->performanceService->getBottleneckAnalysis();
    }

    #[Computed]
    public function performanceTrends()
    {
        return $this->performanceService->getPerformanceTrends($this->selectedPeriod);
    }

    public function updatePeriod($period)
    {
        $this->selectedPeriod = $period;
        $this->dispatch('period-changed', period: $period);
    }

    public function updateMetricType($type)
    {
        $this->metricType = $type;
        $this->dispatch('metric-type-changed', type: $type);
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
        $this->dispatch('auto-refresh-toggled', enabled: $this->autoRefresh);
    }

    public function refreshMetrics()
    {
        $this->performanceService->clearCache();
        $this->dispatch('metrics-refreshed');
    }

    public function exportPerformanceReport($format = 'pdf')
    {
        $data = $this->performanceService->generatePerformanceReport($this->selectedPeriod);
        
        $this->dispatch('download-report', [
            'data' => $data,
            'format' => $format,
            'filename' => 'performance_report_' . $this->selectedPeriod . '_' . now()->format('Y-m-d')
        ]);
    }

    public function render()
    {
        return view('livewire.admin.performance-metrics');
    }
}
