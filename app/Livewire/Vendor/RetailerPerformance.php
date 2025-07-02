<?php

namespace App\Livewire\Vendor;

use App\Services\RetailerAnalyticsService;
use Livewire\Component;
use Livewire\Attributes\On;

class RetailerPerformance extends Component
{
    public $topRetailers = [];
    public $performanceMetrics = [];
    public $orderFrequency = [];
    public $growthTrends = [];
    public $selectedMetric = 'revenue';
    public $timeFrame = 6;

    public function mount()
    {
        $this->loadAnalyticsData();
    }

    #[On('refresh-analytics')]
    public function loadAnalyticsData()
    {
        $retailerService = new RetailerAnalyticsService();

        try {
            $this->topRetailers = $retailerService->getTopRetailers(15)->toArray();
            $this->performanceMetrics = $retailerService->getRetailerPerformanceMetrics();
            $this->orderFrequency = $retailerService->getRetailerOrderFrequency()->toArray();
            $this->growthTrends = $retailerService->getRetailerGrowthTrends($this->timeFrame);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load retailer analytics: ' . $e->getMessage());
        }
    }

    public function updatedTimeFrame()
    {
        $this->loadAnalyticsData();
    }

    public function updatedSelectedMetric()
    {
        // Re-sort retailers based on selected metric
        if ($this->selectedMetric === 'orders') {
            $this->topRetailers = collect($this->topRetailers)
                ->sortByDesc('total_orders')
                ->values()
                ->toArray();
        } else {
            $this->topRetailers = collect($this->topRetailers)
                ->sortByDesc('total_revenue')
                ->values()
                ->toArray();
        }
    }

    public function exportRetailerData()
    {
        try {
            $filename = 'retailer_performance_' . now()->format('Y_m_d') . '.csv';
            $data = collect($this->topRetailers);
            
            $headers = ['name', 'email', 'total_orders', 'total_revenue', 'average_order_value'];
            $csv = $data->map(function ($retailer) {
                return [
                    $retailer['name'],
                    $retailer['email'],
                    $retailer['total_orders'],
                    number_format($retailer['total_revenue'], 2),
                    number_format($retailer['average_order_value'], 2),
                ];
            });

            session()->flash('success', 'Retailer data exported successfully');
            $this->dispatch('download-csv', [
                'filename' => $filename,
                'headers' => $headers,
                'data' => $csv->toArray()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function getRetailerPerformanceChartData()
    {
        return [
            'series' => [
                [
                    'name' => 'New Retailers',
                    'data' => collect($this->growthTrends)->pluck('new_retailers')->toArray(),
                ],
                [
                    'name' => 'Active Retailers',
                    'data' => collect($this->growthTrends)->pluck('active_retailers')->toArray(),
                ],
            ],
            'categories' => collect($this->growthTrends)->pluck('month')->toArray(),
        ];
    }

    public function getOrderFrequencyChartData()
    {
        $frequencyGroups = collect($this->orderFrequency)
            ->groupBy('frequency_category')
            ->map(function ($group) {
                return $group->count();
            });

        return [
            'series' => $frequencyGroups->values()->toArray(),
            'labels' => $frequencyGroups->keys()->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.vendor.retailer-performance');
    }
}
