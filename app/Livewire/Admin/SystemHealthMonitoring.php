<?php

namespace App\Livewire\Admin;

use App\Services\SystemHealthService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class SystemHealthMonitoring extends Component
{
    public $refreshInterval = 5000; // 5 seconds
    public $showDetails = false;
    public $selectedService = null;
    public $alertThreshold = 80; // percentage
    public $enableAlerts = true;
    
    public function mount()
    {
        $this->dispatch('init-health-monitoring');
    }

    #[Computed]
    public function healthService()
    {
        return app(SystemHealthService::class);
    }

    #[Computed]
    public function overallHealth()
    {
        return $this->healthService->getOverallHealthStatus();
    }

    #[Computed]
    public function serviceStatuses()
    {
        return $this->healthService->getServiceStatuses();
    }

    #[Computed]
    public function infrastructureHealth()
    {
        return $this->healthService->getInfrastructureHealth();
    }

    #[Computed]
    public function applicationHealth()
    {
        return $this->healthService->getApplicationHealth();
    }

    #[Computed]
    public function dependencyHealth()
    {
        return $this->healthService->getDependencyHealth();
    }

    #[Computed]
    public function healthTrends()
    {
        return $this->healthService->getHealthTrends();
    }

    #[Computed]
    public function healthAlerts()
    {
        return $this->healthService->getHealthAlerts();
    }

    public function toggleDetails()
    {
        $this->showDetails = !$this->showDetails;
    }

    public function selectService($service)
    {
        $this->selectedService = $service;
        $this->dispatch('service-selected', service: $service);
    }

    public function updateAlertThreshold($threshold)
    {
        $this->alertThreshold = $threshold;
        $this->dispatch('alert-threshold-updated', threshold: $threshold);
    }

    public function toggleAlerts()
    {
        $this->enableAlerts = !$this->enableAlerts;
        $this->dispatch('alerts-toggled', enabled: $this->enableAlerts);
    }

    public function runHealthCheck()
    {
        $this->healthService->runComprehensiveHealthCheck();
        $this->dispatch('health-check-completed');
    }

    public function acknowledgeAlert($alertId)
    {
        $this->healthService->acknowledgeAlert($alertId);
        $this->dispatch('alert-acknowledged', alertId: $alertId);
    }

    public function restartService($serviceName)
    {
        $result = $this->healthService->restartService($serviceName);
        $this->dispatch('service-restart-attempted', [
            'service' => $serviceName,
            'success' => $result
        ]);
    }

    public function exportHealthReport()
    {
        $report = $this->healthService->generateHealthReport();
        
        $this->dispatch('download-health-report', [
            'data' => $report,
            'filename' => 'system_health_report_' . now()->format('Y-m-d_H-i-s')
        ]);
    }

    public function render()
    {
        return view('livewire.admin.system-health-monitoring');
    }
}
