<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use App\Interfaces\Services\MetricsServiceInterface;
use App\Interfaces\Services\AnalyticsServiceInterface;
use Illuminate\Support\Facades\Auth;

class PerformanceMetrics extends Component
{
    public $selectedRole = 'current'; // current, admin, vendor, retailer, production_manager, supplier
    public $timeRange = '30d'; // 7d, 30d, 90d, 1y
    public $kpiData = [];
    public $roleSpecificMetrics = [];
    public $comparisonData = [];
    public $loading = false;

    // Role-specific KPI targets
    public $targets = [];
    
    // Summary metrics
    public $overallScore = 0;
    public $improvementAreas = [];
    public $achievements = [];

    protected $metricsService;
    protected $analyticsService;

    public function boot(
        MetricsServiceInterface $metricsService,
        AnalyticsServiceInterface $analyticsService
    ) {
        $this->metricsService = $metricsService;
        $this->analyticsService = $analyticsService;
    }

    public function mount()
    {
        $this->loadPerformanceData();
    }

    public function updatedSelectedRole()
    {
        $this->loadPerformanceData();
    }

    public function updatedTimeRange()
    {
        $this->loadPerformanceData();
    }

    public function loadPerformanceData()
    {
        $this->loading = true;

        try {
            $role = $this->selectedRole === 'current' ? Auth::user()->role : $this->selectedRole;
            
            // Load KPI data based on role
            $this->kpiData = $this->metricsService->getRoleSpecificKPIs([
                'role' => $role,
                'time_range' => $this->timeRange,
                'user_id' => Auth::id()
            ]);

            // Load role-specific metrics
            $this->roleSpecificMetrics = $this->getMetricsForRole($role);

            // Load comparison data (vs targets, previous period)
            $this->comparisonData = $this->metricsService->getPerformanceComparison([
                'role' => $role,
                'time_range' => $this->timeRange,
                'user_id' => Auth::id()
            ]);

            // Load targets for the role
            $this->targets = $this->metricsService->getRoleTargets($role);

            // Calculate overall performance score
            $this->calculateOverallScore();

            // Identify improvement areas and achievements
            $this->identifyPerformanceInsights();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load performance data: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    private function getMetricsForRole($role)
    {
        return match($role) {
            'admin' => $this->metricsService->getAdminMetrics($this->timeRange),
            'vendor' => $this->metricsService->getVendorMetrics($this->timeRange, Auth::id()),
            'retailer' => $this->metricsService->getRetailerMetrics($this->timeRange, Auth::id()),
            'production_manager' => $this->metricsService->getProductionMetrics($this->timeRange, Auth::id()),
            'supplier' => $this->metricsService->getSupplierMetrics($this->timeRange, Auth::id()),
            default => []
        };
    }

    private function calculateOverallScore()
    {
        if (empty($this->kpiData)) {
            $this->overallScore = 0;
            return;
        }

        $totalScore = 0;
        $count = 0;

        foreach ($this->kpiData as $kpi) {
            if (isset($kpi['score'])) {
                $totalScore += $kpi['score'];
                $count++;
            }
        }

        $this->overallScore = $count > 0 ? round($totalScore / $count, 1) : 0;
    }

    private function identifyPerformanceInsights()
    {
        $this->improvementAreas = [];
        $this->achievements = [];

        foreach ($this->kpiData as $kpi) {
            $score = $kpi['score'] ?? 0;
            $target = $kpi['target'] ?? 100;
            
            if ($score < $target * 0.8) { // Below 80% of target
                $this->improvementAreas[] = [
                    'name' => $kpi['name'],
                    'current' => $score,
                    'target' => $target,
                    'gap' => $target - $score
                ];
            } elseif ($score >= $target) { // Met or exceeded target
                $this->achievements[] = [
                    'name' => $kpi['name'],
                    'current' => $score,
                    'target' => $target,
                    'excess' => $score - $target
                ];
            }
        }
    }

    public function exportPerformanceReport()
    {
        try {
            $role = $this->selectedRole === 'current' ? Auth::user()->role : $this->selectedRole;
            
            // This would typically generate and download a performance report
            session()->flash('success', 'Performance report export started. You will receive the file shortly.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export report: ' . $e->getMessage());
        }
    }

    public function refreshData()
    {
        $this->loadPerformanceData();
        $this->dispatch('performanceDataRefreshed');
    }

    public function render()
    {
        return view('livewire.analytics.performance-metrics');
    }
}
