<?php

namespace App\Livewire\ProductionManager;

use App\Services\ProductionEfficiencyService;
use App\Services\AnalyticsService;
use Livewire\Component;

class ProductionAnalytics extends Component
{
    public $selectedPeriod = '30d';
    public $selectedMetric = 'efficiency';
    public $chartData = [];
    public $comparisonData = [];
    public $kpiMetrics = [];

    public $periods = [
        '7d' => 'Last 7 Days',
        '30d' => 'Last 30 Days',
        '90d' => 'Last 3 Months',
        '1y' => 'Last Year'
    ];

    public $metrics = [
        'efficiency' => 'Production Efficiency',
        'throughput' => 'Throughput',
        'quality' => 'Quality Score',
        'downtime' => 'Downtime Analysis',
        'cost' => 'Cost Analysis'
    ];

    protected $productionService;
    protected $analyticsService;

    public function boot(
        ProductionEfficiencyService $productionService,
        AnalyticsService $analyticsService
    ) {
        $this->productionService = $productionService;
        $this->analyticsService = $analyticsService;
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

    public function loadAnalytics()
    {
        $period = $this->getPeriodDate();
        
        switch ($this->selectedMetric) {
            case 'efficiency':
                $this->loadEfficiencyAnalytics($period);
                break;
            case 'throughput':
                $this->loadThroughputAnalytics($period);
                break;
            case 'quality':
                $this->loadQualityAnalytics($period);
                break;
            case 'downtime':
                $this->loadDowntimeAnalytics($period);
                break;
            case 'cost':
                $this->loadCostAnalytics($period);
                break;
        }

        $this->loadKPIMetrics($period);
        $this->loadComparisonData($period);
    }

    private function loadEfficiencyAnalytics($period)
    {
        $this->chartData = [
            'series' => [[
                'name' => 'Efficiency %',
                'data' => $this->productionService->getEfficiencyTrendData($period)
            ]],
            'categories' => $this->productionService->getDateLabels($period),
            'type' => 'line'
        ];
    }

    private function loadThroughputAnalytics($period)
    {
        $this->chartData = [
            'series' => [[
                'name' => 'Units Produced',
                'data' => $this->productionService->getThroughputData($period)
            ]],
            'categories' => $this->productionService->getDateLabels($period),
            'type' => 'column'
        ];
    }

    private function loadQualityAnalytics($period)
    {
        $this->chartData = [
            'series' => [
                [
                    'name' => 'Quality Score',
                    'data' => $this->productionService->getQualityScoreData($period)
                ],
                [
                    'name' => 'Defect Rate',
                    'data' => $this->productionService->getDefectRateData($period)
                ]
            ],
            'categories' => $this->productionService->getDateLabels($period),
            'type' => 'line'
        ];
    }

    private function loadDowntimeAnalytics($period)
    {
        $downtimeData = $this->productionService->getDowntimeAnalysis($period);
        
        $this->chartData = [
            'series' => array_values($downtimeData['breakdown']),
            'labels' => array_keys($downtimeData['breakdown']),
            'type' => 'donut'
        ];
    }

    private function loadCostAnalytics($period)
    {
        $costData = $this->productionService->getCostAnalysis($period);
        
        $this->chartData = [
            'series' => [
                [
                    'name' => 'Total Cost',
                    'data' => $costData['total_cost_trend']
                ],
                [
                    'name' => 'Cost per Unit',
                    'data' => $costData['cost_per_unit_trend']
                ]
            ],
            'categories' => $this->productionService->getDateLabels($period),
            'type' => 'line'
        ];
    }

    private function loadKPIMetrics($period)
    {
        $this->kpiMetrics = [
            'overall_efficiency' => $this->productionService->getOverallEfficiency($period),
            'avg_throughput' => $this->productionService->getAverageThroughput($period),
            'quality_score' => $this->productionService->getQualityScore($period),
            'total_downtime' => $this->productionService->getTotalDowntime($period),
            'cost_per_unit' => $this->productionService->getCostPerUnit($period),
            'on_time_delivery' => $this->productionService->getOnTimeDeliveryRate($period)
        ];
    }

    private function loadComparisonData($period)
    {
        $previousPeriod = $this->getPreviousPeriodDate($period);
        
        $this->comparisonData = [
            'current' => $this->productionService->getPeriodSummary($period),
            'previous' => $this->productionService->getPeriodSummary($previousPeriod)
        ];
    }

    private function getPeriodDate()
    {
        return match($this->selectedPeriod) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };
    }

    private function getPreviousPeriodDate($currentPeriod)
    {
        return match($this->selectedPeriod) {
            '7d' => now()->subDays(14),
            '30d' => now()->subDays(60),
            '90d' => now()->subDays(180),
            '1y' => now()->subYears(2),
            default => now()->subDays(60),
        };
    }

    public function exportAnalytics()
    {
        try {
            $data = [
                'period' => $this->selectedPeriod,
                'metric' => $this->selectedMetric,
                'chart_data' => $this->chartData,
                'kpi_metrics' => $this->kpiMetrics,
                'comparison_data' => $this->comparisonData
            ];

            $filename = 'production_analytics_' . date('Y-m-d_H-i-s') . '.json';
            
            $this->dispatch('download-file', [
                'filename' => $filename,
                'content' => json_encode($data, JSON_PRETTY_PRINT),
                'type' => 'application/json'
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Analytics exported successfully'
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
        return view('livewire.production-manager.production-analytics');
    }
}
