<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use App\Interfaces\Services\AnalyticsServiceInterface;
use App\Interfaces\Services\ReportServiceInterface;
use Carbon\Carbon;

class SalesTrendsChart extends Component
{
    public $timeRange = '30d'; // 7d, 30d, 90d, 1y
    public $chartType = 'line'; // line, bar, area
    public $salesData = [];
    public $totalSales = 0;
    public $salesGrowth = 0;
    public $topProducts = [];
    public $loading = false;

    protected $analyticsService;
    protected $reportService;

    public function boot(
        AnalyticsServiceInterface $analyticsService,
        ReportServiceInterface $reportService
    ) {
        $this->analyticsService = $analyticsService;
        $this->reportService = $reportService;
    }

    public function mount()
    {
        $this->loadSalesData();
    }

    public function updatedTimeRange()
    {
        $this->loadSalesData();
    }

    public function updatedChartType()
    {
        $this->dispatch('updateChart', [
            'type' => $this->chartType,
            'data' => $this->salesData
        ]);
    }

    public function loadSalesData()
    {
        $this->loading = true;

        try {
            $dateRange = $this->getDateRange();
            
            // Get sales trends data
            $this->salesData = $this->analyticsService->getSalesTrends([
                'start_date' => $dateRange['start'],
                'end_date' => $dateRange['end'],
                'group_by' => $this->getGroupBy()
            ]);

            // Get sales summary
            $summary = $this->analyticsService->getSalesSummary([
                'start_date' => $dateRange['start'],
                'end_date' => $dateRange['end']
            ]);

            $this->totalSales = $summary['total_sales'] ?? 0;
            $this->salesGrowth = $summary['growth_percentage'] ?? 0;

            // Get top products
            $this->topProducts = $this->reportService->getTopSellingProducts([
                'start_date' => $dateRange['start'],
                'end_date' => $dateRange['end'],
                'limit' => 5
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load sales data: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function exportChart()
    {
        try {
            $dateRange = $this->getDateRange();
            $this->reportService->exportSalesReport([
                'start_date' => $dateRange['start'],
                'end_date' => $dateRange['end'],
                'format' => 'excel'
            ]);
            
            session()->flash('success', 'Sales report export started. You will receive the file shortly.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export sales report: ' . $e->getMessage());
        }
    }

    private function getDateRange(): array
    {
        $end = Carbon::now();
        
        $start = match($this->timeRange) {
            '7d' => $end->copy()->subDays(7),
            '30d' => $end->copy()->subDays(30),
            '90d' => $end->copy()->subDays(90),
            '1y' => $end->copy()->subYear(),
            default => $end->copy()->subDays(30)
        };

        return [
            'start' => $start,
            'end' => $end
        ];
    }

    private function getGroupBy(): string
    {
        return match($this->timeRange) {
            '7d' => 'day',
            '30d' => 'day',
            '90d' => 'week',
            '1y' => 'month',
            default => 'day'
        };
    }

    public function render()
    {
        return view('livewire.analytics.sales-trends-chart');
    }
}
