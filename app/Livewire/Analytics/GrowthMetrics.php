<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use App\Interfaces\Services\AnalyticsServiceInterface;
use App\Interfaces\Services\MetricsServiceInterface;
use Carbon\Carbon;

class GrowthMetrics extends Component
{
    public $timeRange = '12m'; // 3m, 6m, 12m, 24m
    public $metricType = 'users'; // users, orders, revenue
    public $growthData = [];
    public $currentPeriodTotal = 0;
    public $previousPeriodTotal = 0;
    public $growthPercentage = 0;
    public $trendDirection = 'up';
    public $loading = false;

    // Additional metrics
    public $newUsersCount = 0;
    public $activeUsersCount = 0;
    public $userRetentionRate = 0;
    public $orderGrowthRate = 0;
    public $revenueGrowthRate = 0;

    protected $analyticsService;
    protected $metricsService;

    public function boot(
        AnalyticsServiceInterface $analyticsService,
        MetricsServiceInterface $metricsService
    ) {
        $this->analyticsService = $analyticsService;
        $this->metricsService = $metricsService;
    }

    public function mount()
    {
        $this->loadGrowthData();
    }

    public function updatedTimeRange()
    {
        $this->loadGrowthData();
    }

    public function updatedMetricType()
    {
        $this->loadGrowthData();
    }

    public function loadGrowthData()
    {
        $this->loading = true;

        try {
            $dateRange = $this->getDateRange();
            
            // Get growth data based on metric type
            $this->growthData = $this->analyticsService->getGrowthMetrics([
                'metric_type' => $this->metricType,
                'start_date' => $dateRange['start'],
                'end_date' => $dateRange['end'],
                'group_by' => $this->getGroupBy()
            ]);

            // Calculate growth percentage
            $this->calculateGrowthMetrics();

            // Load additional metrics
            $this->loadAdditionalMetrics($dateRange);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load growth data: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    private function calculateGrowthMetrics()
    {
        if (count($this->growthData) < 2) {
            return;
        }

        $dataCount = count($this->growthData);
        $midPoint = intval($dataCount / 2);

        // Calculate current and previous period totals
        $currentPeriod = array_slice($this->growthData, $midPoint);
        $previousPeriod = array_slice($this->growthData, 0, $midPoint);

        $this->currentPeriodTotal = array_sum(array_column($currentPeriod, 'value'));
        $this->previousPeriodTotal = array_sum(array_column($previousPeriod, 'value'));

        // Calculate growth percentage
        if ($this->previousPeriodTotal > 0) {
            $this->growthPercentage = (($this->currentPeriodTotal - $this->previousPeriodTotal) / $this->previousPeriodTotal) * 100;
        } else {
            $this->growthPercentage = $this->currentPeriodTotal > 0 ? 100 : 0;
        }

        $this->trendDirection = $this->growthPercentage >= 0 ? 'up' : 'down';
    }

    private function loadAdditionalMetrics($dateRange)
    {
        // Load user metrics
        $userMetrics = $this->metricsService->getUserGrowthMetrics([
            'start_date' => $dateRange['start'],
            'end_date' => $dateRange['end']
        ]);

        $this->newUsersCount = $userMetrics['new_users'] ?? 0;
        $this->activeUsersCount = $userMetrics['active_users'] ?? 0;
        $this->userRetentionRate = $userMetrics['retention_rate'] ?? 0;

        // Load order metrics
        $orderMetrics = $this->metricsService->getOrderGrowthMetrics([
            'start_date' => $dateRange['start'],
            'end_date' => $dateRange['end']
        ]);

        $this->orderGrowthRate = $orderMetrics['growth_rate'] ?? 0;

        // Load revenue metrics
        $revenueMetrics = $this->metricsService->getRevenueGrowthMetrics([
            'start_date' => $dateRange['start'],
            'end_date' => $dateRange['end']
        ]);

        $this->revenueGrowthRate = $revenueMetrics['growth_rate'] ?? 0;
    }

    private function getDateRange(): array
    {
        $end = Carbon::now();
        
        $start = match($this->timeRange) {
            '3m' => $end->copy()->subMonths(3),
            '6m' => $end->copy()->subMonths(6),
            '12m' => $end->copy()->subMonths(12),
            '24m' => $end->copy()->subMonths(24),
            default => $end->copy()->subMonths(12)
        };

        return [
            'start' => $start,
            'end' => $end
        ];
    }

    private function getGroupBy(): string
    {
        return match($this->timeRange) {
            '3m' => 'week',
            '6m' => 'week',
            '12m' => 'month',
            '24m' => 'month',
            default => 'month'
        };
    }

    public function refreshData()
    {
        $this->loadGrowthData();
        $this->dispatch('growthDataRefreshed');
    }

    public function render()
    {
        return view('livewire.analytics.growth-metrics');
    }
}
