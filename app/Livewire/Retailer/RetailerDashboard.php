<?php

namespace App\Livewire\Retailer;

use App\Services\RetailerSalesService;
use App\Services\RetailerInventoryService;
use Livewire\Component;
use Livewire\Attributes\On;

class RetailerDashboard extends Component
{
    public $salesMetrics = [];
    public $salesTrends = [];
    public $topProducts = [];
    public $purchasePatterns = [];
    public $recentActivity = [];
    public $inventoryRecommendations = [];
    public $purchaseAnalytics = [];
    public $timeFrame = 6;

    public function mount()
    {
        $this->loadDashboardData();
    }

    #[On('refresh-dashboard')]
    public function loadDashboardData()
    {
        $salesService = new RetailerSalesService();
        $inventoryService = new RetailerInventoryService();

        try {
            $this->salesMetrics = $salesService->getSalesMetrics();
            $this->salesTrends = $salesService->getSalesTrends($this->timeFrame);
            $this->topProducts = $salesService->getTopPurchasedProducts(8)->toArray();
            $this->purchasePatterns = $salesService->getPurchasePatterns();
            $this->recentActivity = $salesService->getRecentOrderActivity(6);
            $this->inventoryRecommendations = $inventoryService->getInventoryRecommendations();
            $this->purchaseAnalytics = $inventoryService->getPurchaseAnalytics();
            
            // Ensure all required keys exist in salesMetrics with default values
            $this->salesMetrics = array_merge([
                'total_orders' => 0,
                'total_revenue' => 0,
                'average_order_value' => 0,
                'orders_this_month' => 0,
                'order_growth_percentage' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
            ], $this->salesMetrics);
            
        } catch (\Exception $e) {
            // Set default values if service fails
            $this->salesMetrics = [
                'total_orders' => 0,
                'total_revenue' => 0,
                'average_order_value' => 0,
                'orders_this_month' => 0,
                'order_growth_percentage' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
            ];
            $this->salesTrends = [];
            $this->topProducts = [];
            $this->purchasePatterns = [];
            $this->recentActivity = [];
            $this->inventoryRecommendations = [];
            $this->purchaseAnalytics = [];
            
            session()->flash('error', 'Failed to load dashboard data: ' . $e->getMessage());
        }
    }

    public function updatedTimeFrame()
    {
        $this->loadDashboardData();
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        session()->flash('success', 'Dashboard data refreshed successfully');
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
                    'data' => collect($this->salesTrends)->pluck('revenue')->map(function ($value) {
                        return round($value / 100, 2); // Convert to hundreds for better chart display
                    })->toArray(),
                ],
            ],
            'categories' => collect($this->salesTrends)->pluck('month')->toArray(),
        ];
    }

    public function getPurchasePatternsChartData()
    {
        $dayPatterns = $this->purchasePatterns['day_patterns'] ?? [];
        
        return [
            'series' => [array_values($dayPatterns)],
            'categories' => array_keys($dayPatterns),
        ];
    }

    public function getTopProductsChartData()
    {
        $topProducts = collect($this->topProducts)->take(5);
        
        return [
            'series' => $topProducts->pluck('total_quantity')->toArray(),
            'labels' => $topProducts->pluck('product_name')->toArray(),
        ];
    }

    public function getInventoryHealthScore()
    {
        $analytics = $this->purchaseAnalytics;
        
        $diversityScore = $analytics['inventory_diversity_score'] ?? 0;
        $velocityScore = min(100, ($analytics['purchase_velocity'] ?? 0) * 100);
        $consistencyScore = 75; // Placeholder - could be calculated from purchase patterns
        
        return round(($diversityScore + $velocityScore + $consistencyScore) / 3, 1);
    }

    public function getQuickStats()
    {
        return [
            'pending_orders' => $this->salesMetrics['pending_orders'] ?? 0,
            'total_products_purchased' => $this->purchaseAnalytics['total_unique_products'] ?? 0,
            'avg_order_size' => round($this->purchaseAnalytics['average_order_size'] ?? 0, 1),
            'purchase_frequency' => $this->purchasePatterns['purchase_frequency_category'] ?? 'Unknown',
        ];
    }

    public function render()
    {
        return view('livewire.retailer.retailer-dashboard');
    }
}
