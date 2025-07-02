<?php

namespace App\Livewire\Retailer;

use App\Services\RetailerInventoryService;
use Livewire\Component;
use Livewire\Attributes\On;

class InventoryRecommendations extends Component
{
    public $recommendations = [];
    public $purchaseAnalytics = [];
    public $stockOptimization = [];
    public $performanceMetrics = [];
    public $selectedCategory = 'all';
    public $priorityFilter = 'all';

    public function mount()
    {
        $this->loadRecommendations();
    }

    #[On('refresh-recommendations')]
    public function loadRecommendations()
    {
        $inventoryService = new RetailerInventoryService();

        try {
            $this->recommendations = $inventoryService->getInventoryRecommendations();
            $this->purchaseAnalytics = $inventoryService->getPurchaseAnalytics();
            $this->stockOptimization = $inventoryService->getStockOptimizationSuggestions()->toArray();
            $this->performanceMetrics = $inventoryService->getInventoryPerformanceMetrics();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load recommendations: ' . $e->getMessage());
        }
    }

    public function updatedSelectedCategory()
    {
        $this->filterRecommendations();
    }

    public function updatedPriorityFilter()
    {
        $this->filterRecommendations();
    }

    public function filterRecommendations()
    {
        // This would filter recommendations based on category and priority
        $this->loadRecommendations();
    }

    public function exportRecommendations()
    {
        try {
            $filename = 'inventory_recommendations_' . now()->format('Y_m_d') . '.csv';
            $data = collect($this->recommendations['frequent_replenishment'] ?? []);
            
            $headers = ['product_name', 'order_frequency', 'days_since_last_order', 'suggested_quantity', 'recommendation_score'];
            
            $csv = $data->map(function ($item) {
                return [
                    $item['product']->name ?? 'Unknown',
                    $item['order_frequency'] ?? 0,
                    $item['days_since_last_order'] ?? 0,
                    $item['suggested_quantity'] ?? 0,
                    round($item['recommendation_score'] ?? 0, 1),
                ];
            });

            session()->flash('success', 'Recommendations exported successfully');
            $this->dispatch('download-csv', [
                'filename' => $filename,
                'headers' => $headers,
                'data' => $csv->toArray()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export recommendations: ' . $e->getMessage());
        }
    }

    public function getRecommendationStats()
    {
        $frequentCount = count($this->recommendations['frequent_replenishment'] ?? []);
        $seasonalCount = count($this->recommendations['seasonal_opportunities'] ?? []);
        $trendingCount = count($this->recommendations['trending_products'] ?? []);
        $alertsCount = count($this->recommendations['low_stock_alerts'] ?? []);
        
        return [
            'frequent_replenishment' => $frequentCount,
            'seasonal_opportunities' => $seasonalCount,
            'trending_products' => $trendingCount,
            'low_stock_alerts' => $alertsCount,
            'total_recommendations' => $frequentCount + $seasonalCount + $trendingCount,
        ];
    }

    public function getCategoryDistribution()
    {
        $categories = $this->purchaseAnalytics['top_categories'] ?? [];
        
        return [
            'series' => array_values($categories),
            'labels' => array_keys($categories),
        ];
    }

    public function getPerformanceScoreData()
    {
        $metrics = $this->performanceMetrics;
        
        return [
            'order_frequency' => min(100, ($metrics['orders_frequency'] ?? 0) * 10),
            'consistency' => $metrics['order_consistency_score'] ?? 0,
            'diversification' => min(100, ($metrics['category_diversification'] ?? 0) * 20),
            'turnover' => min(100, ($metrics['inventory_turnover_estimate'] ?? 0) * 5),
        ];
    }

    public function render()
    {
        return view('livewire.retailer.inventory-recommendations');
    }
}
