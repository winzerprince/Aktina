<?php

namespace App\Livewire\Vendor;

use App\Services\RetailerAnalyticsService;
use App\Services\VendorRetailerService;
use Livewire\Component;
use Livewire\Attributes\On;

class RetailerPerformance extends Component
{
    public $topRetailers = [];
    public $performanceMetrics = [];
    public $orderFrequency = [];
    public $growthTrends = [];
    public $connectedRetailers = [];
    public $connectionSummary = [];
    public $selectedMetric = 'revenue';
    public $timeFrame = 6;
    public $vendorId;

    public function mount()
    {
        // Get current vendor's ID
        $this->vendorId = auth()->user()->vendor?->id;

        if (!$this->vendorId) {
            session()->flash('error', 'Vendor profile not found');
            return;
        }

        $this->loadAnalyticsData();
    }

    #[On('refresh-analytics')]
    public function loadAnalyticsData()
    {
        if (!$this->vendorId) {
            return;
        }

        $retailerService = new RetailerAnalyticsService();
        $vendorRetailerService = new VendorRetailerService();

        try {
            // Load vendor-specific data
            $this->connectedRetailers = $vendorRetailerService->getConnectedRetailers($this->vendorId)->toArray();
            $this->connectionSummary = $vendorRetailerService->getConnectionSummary($this->vendorId);
            $this->topRetailers = $vendorRetailerService->getTopRetailersForVendor($this->vendorId, 15)->toArray();

            // Use vendor-specific analytics methods
            $this->performanceMetrics = $retailerService->getRetailerPerformanceMetricsForVendor($this->vendorId);
            $this->orderFrequency = $retailerService->getRetailerOrderFrequencyForVendor($this->vendorId)->toArray();

            // Keep general growth trends for now (can be vendor-specific later if needed)
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
            $filename = 'connected_retailers_' . now()->format('Y_m_d') . '.csv';
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

            session()->flash('success', 'Connected retailer data exported successfully');
            $this->dispatch('download-csv', [
                'filename' => $filename,
                'headers' => $headers,
                'data' => $csv->toArray()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function getConnectionSummaryData()
    {
        $summary = $this->connectionSummary;

        return [
            'series' => array_values($summary),
            'labels' => array_map('ucfirst', array_keys($summary)),
        ];
    }

    public function getVendorMetricsData()
    {
        $metrics = $this->performanceMetrics;

        return [
            'total_connected' => $metrics['total_retailers'] ?? 0,
            'active_retailers' => $metrics['active_retailers'] ?? 0,
            'active_percentage' => round($metrics['active_percentage'] ?? 0, 1),
            'avg_orders' => $metrics['average_orders_per_retailer'] ?? 0,
            'retention_rate' => round($metrics['retailer_retention_rate'] ?? 0, 1),
        ];
    }

    public function getRetailerPerformanceChartData()
    {
        // Use vendor-specific growth trends if available
        return [
            'series' => [
                [
                    'name' => 'Connected Retailers',
                    'data' => collect($this->growthTrends)->pluck('active_retailers')->toArray(),
                ],
                [
                    'name' => 'Total Orders',
                    'data' => collect($this->growthTrends)->pluck('total_orders')->toArray(),
                ],
            ],
            'categories' => collect($this->growthTrends)->pluck('month')->toArray(),
        ];
    }

    public function getOrderFrequencyChartData()
    {
        if (empty($this->orderFrequency)) {
            return [
                'series' => [],
                'labels' => [],
            ];
        }

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

    public function refreshData()
    {
        $this->loadAnalyticsData();
        session()->flash('success', 'Retailer data refreshed successfully');
    }

    public function hasConnectedRetailers()
    {
        return !empty($this->connectedRetailers);
    }

    public function render()
    {
        return view('livewire.vendor.retailer-performance');
    }
}
