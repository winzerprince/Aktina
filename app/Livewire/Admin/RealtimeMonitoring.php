<?php

namespace App\Livewire\Admin;

use App\Services\RealtimeMonitoringService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class RealtimeMonitoring extends Component
{
    public $refreshInterval = 3000; // 3 seconds
    public $activeMetrics = ['system', 'database', 'cache', 'queue'];
    public $alertsEnabled = true;
    public $showLogs = false;
    
    public function mount()
    {
        $this->dispatch('init-monitoring-charts');
    }

    #[Computed]
    public function monitoringService()
    {
        return app(RealtimeMonitoringService::class);
    }

    #[Computed]
    public function systemMetrics()
    {
        return $this->monitoringService->getSystemMetrics();
    }

    #[Computed]
    public function databaseMetrics()
    {
        return $this->monitoringService->getDatabaseMetrics();
    }

    #[Computed]
    public function cacheMetrics()
    {
        return $this->monitoringService->getCacheMetrics();
    }

    #[Computed]
    public function queueMetrics()
    {
        return $this->monitoringService->getQueueMetrics();
    }

    #[Computed]
    public function performanceTimeline()
    {
        return $this->monitoringService->getPerformanceTimeline();
    }

    #[Computed]
    public function systemAlerts()
    {
        return $this->monitoringService->getSystemAlerts();
    }

    #[Computed]
    public function recentLogs()
    {
        return $this->monitoringService->getRecentLogs();
    }

    #[Computed]
    public function errorRates()
    {
        return $this->monitoringService->getErrorRates();
    }

    public function toggleMetric($metric)
    {
        if (in_array($metric, $this->activeMetrics)) {
            $this->activeMetrics = array_diff($this->activeMetrics, [$metric]);
        } else {
            $this->activeMetrics[] = $metric;
        }
        $this->dispatch('metrics-toggled', metrics: $this->activeMetrics);
    }

    public function updateRefreshInterval($interval)
    {
        $this->refreshInterval = $interval;
        $this->dispatch('refresh-interval-changed', interval: $interval);
    }

    public function toggleAlerts()
    {
        $this->alertsEnabled = !$this->alertsEnabled;
        $this->dispatch('alerts-toggled', enabled: $this->alertsEnabled);
    }

    public function toggleLogs()
    {
        $this->showLogs = !$this->showLogs;
    }

    public function acknowledgeAlert($alertId)
    {
        $this->monitoringService->acknowledgeAlert($alertId);
        $this->dispatch('alert-acknowledged', alertId: $alertId);
    }

    public function clearLogs()
    {
        $this->monitoringService->clearLogs();
        $this->dispatch('logs-cleared');
    }

    public function exportMetrics($format = 'json')
    {
        $data = $this->monitoringService->exportMetrics();
        
        $this->dispatch('download-metrics', [
            'data' => $data,
            'format' => $format,
            'filename' => 'system_metrics_' . now()->format('Y-m-d_H-i-s')
        ]);
    }

    public function formatBytes($size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    public function render()
    {
        return view('livewire.admin.realtime-monitoring');
    }
}
