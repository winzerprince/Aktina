<?php

namespace App\Livewire\Retailer;

use App\Services\RetailerSalesService;
use Livewire\Component;
use Livewire\Attributes\On;

class PurchaseHistoryAnalytics extends Component
{
    public $salesTrends = [];
    public $topProducts = [];
    public $purchasePatterns = [];
    public $seasonalTrends = [];
    public $orderStatusBreakdown = [];
    public $timeFrame = 6;
    public $selectedView = 'trends';

    public function mount()
    {
        $this->loadAnalyticsData();
    }

    #[On('refresh-analytics')]
    public function loadAnalyticsData()
    {
        $salesService = new RetailerSalesService();

        try {
            $this->salesTrends = $salesService->getSalesTrends($this->timeFrame);
            $this->topProducts = $salesService->getTopPurchasedProducts(15)->toArray();
            $this->purchasePatterns = $salesService->getPurchasePatterns();
            $this->seasonalTrends = $salesService->getSeasonalTrends();
            $this->orderStatusBreakdown = $salesService->getOrderStatusBreakdown()->toArray();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load analytics data: ' . $e->getMessage());
        }
    }

    public function updatedTimeFrame()
    {
        $this->loadAnalyticsData();
    }

    public function switchView($view)
    {
        $this->selectedView = $view;
    }

    public function exportAnalytics()
    {
        try {
            $filename = 'purchase_analytics_' . now()->format('Y_m_d') . '.csv';
            
            if ($this->selectedView === 'products') {
                $data = collect($this->topProducts);
                $headers = ['product_name', 'product_sku', 'total_quantity', 'total_spent', 'average_price'];
            } else {
                $data = collect($this->salesTrends);
                $headers = ['month', 'orders', 'revenue', 'average_order_value'];
            }
            
            $csv = $data->map(function ($item) use ($headers) {
                return array_map(function ($key) use ($item) {
                    return $item[$key] ?? '';
                }, $headers);
            });

            session()->flash('success', 'Analytics data exported successfully');
            $this->dispatch('download-csv', [
                'filename' => $filename,
                'headers' => $headers,
                'data' => $csv->toArray()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function getSalesTrendsChartData()
    {
        return [
            'series' => [
                [
                    'name' => 'Orders',
                    'data' => collect($this->salesTrends)->pluck('orders')->toArray(),
                ],
                [
                    'name' => 'Revenue',
                    'data' => collect($this->salesTrends)->pluck('revenue')->toArray(),
                ],
            ],
            'categories' => collect($this->salesTrends)->pluck('month')->toArray(),
        ];
    }

    public function getSeasonalTrendsChartData()
    {
        return [
            'series' => [
                [
                    'name' => 'Orders',
                    'data' => collect($this->seasonalTrends)->pluck('orders')->toArray(),
                ],
                [
                    'name' => 'Revenue',
                    'data' => collect($this->seasonalTrends)->pluck('revenue')->toArray(),
                ],
            ],
            'categories' => collect($this->seasonalTrends)->pluck('quarter')->toArray(),
        ];
    }

    public function getOrderStatusChartData()
    {
        $statusData = collect($this->orderStatusBreakdown);
        
        return [
            'series' => $statusData->pluck('count')->toArray(),
            'labels' => $statusData->keys()->map(function ($status) {
                return ucfirst($status);
            })->toArray(),
        ];
    }

    public function getPurchasePatternsData()
    {
        return [
            'day_patterns' => $this->purchasePatterns['day_patterns'] ?? [],
            'hour_patterns' => $this->purchasePatterns['hour_patterns'] ?? [],
            'avg_days_between_orders' => $this->purchasePatterns['average_days_between_orders'] ?? 0,
            'frequency_category' => $this->purchasePatterns['purchase_frequency_category'] ?? 'Unknown',
        ];
    }

    public function render()
    {
        return view('livewire.retailer.purchase-history-analytics');
    }
}
