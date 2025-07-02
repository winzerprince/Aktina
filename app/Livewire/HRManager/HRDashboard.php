<?php

namespace App\Livewire\HRManager;

use App\Services\HRService;
use Livewire\Component;

class HRDashboard extends Component
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
        $hrService = app(HRService::class);

        $employeeStats = $hrService->getEmployeeStats();
        $workforceAnalytics = $hrService->getWorkforceAnalytics();
        $departmentMetrics = $hrService->getDepartmentMetrics();
        $activityTrends = $hrService->getEmployeeActivityTrends((int)$this->timeframe);
        $trainingNeeds = $hrService->getTrainingNeeds();

        return view('livewire.hr-manager.hr-dashboard', [
            'employeeStats' => $employeeStats,
            'workforceAnalytics' => $workforceAnalytics,
            'departmentMetrics' => $departmentMetrics,
            'activityTrends' => $activityTrends,
            'trainingNeeds' => $trainingNeeds,
        ]);
    }
}
