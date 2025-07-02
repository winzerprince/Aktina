<?php

namespace App\Livewire\Vendor;

use App\Services\VendorInventoryService;
use Livewire\Component;
use Livewire\Attributes\On;

class VendorInventoryDashboard extends Component
{
    public $turnoverMetrics = [];
    public $productMovement = [];
    public $turnoverTrends = [];
    public $stockAnalysis = [];
    public $reorderRecommendations = [];
    public $selectedView = 'overview';
    public $timeFrame = 6;
    public $sortBy = 'turnover_rate';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->loadInventoryData();
    }

    #[On('refresh-inventory')]
    public function loadInventoryData()
    {
        $inventoryService = new VendorInventoryService();

        try {
            $this->turnoverMetrics = $inventoryService->getInventoryTurnoverMetrics();
            $this->productMovement = $inventoryService->getProductMovementData(25)->toArray();
            $this->turnoverTrends = $inventoryService->getInventoryTurnoverTrends($this->timeFrame);
            $this->stockAnalysis = $inventoryService->getStockLevelAnalysis();
            $this->reorderRecommendations = $inventoryService->getReorderRecommendations()->toArray();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load inventory data: ' . $e->getMessage());
        }
    }

    public function updatedTimeFrame()
    {
        $this->loadInventoryData();
    }

    public function updatedSortBy()
    {
        $this->sortProductMovement();
    }

    public function updatedSortDirection()
    {
        $this->sortProductMovement();
    }

    public function sortProductMovement()
    {
        $this->productMovement = collect($this->productMovement)
            ->sortBy($this->sortBy, SORT_REGULAR, $this->sortDirection === 'desc')
            ->values()
            ->toArray();
    }

    public function switchView($view)
    {
        $this->selectedView = $view;
    }

    public function exportInventoryData()
    {
        try {
            $filename = 'inventory_analysis_' . now()->format('Y_m_d') . '.csv';
            
            if ($this->selectedView === 'movement') {
                $data = collect($this->productMovement);
                $headers = ['name', 'sku', 'current_stock', 'total_sold', 'turnover_rate', 'movement_category', 'reorder_status'];
            } else {
                $data = collect($this->reorderRecommendations);
                $headers = ['name', 'sku', 'current_stock', 'recommended_quantity', 'priority'];
            }
            
            $csv = $data->map(function ($item) use ($headers) {
                return array_map(function ($key) use ($item) {
                    return $item[$key] ?? '';
                }, $headers);
            });

            session()->flash('success', 'Inventory data exported successfully');
            $this->dispatch('download-csv', [
                'filename' => $filename,
                'headers' => $headers,
                'data' => $csv->toArray()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function getTurnoverTrendsChartData()
    {
        return [
            'series' => [
                [
                    'name' => 'Turnover Ratio',
                    'data' => collect($this->turnoverTrends)->pluck('turnover_ratio')->map(function ($value) {
                        return round($value, 2);
                    })->toArray(),
                ],
                [
                    'name' => 'Total Revenue',
                    'data' => collect($this->turnoverTrends)->pluck('total_revenue')->map(function ($value) {
                        return round($value / 1000, 1); // Convert to thousands
                    })->toArray(),
                ],
            ],
            'categories' => collect($this->turnoverTrends)->pluck('month')->toArray(),
        ];
    }

    public function getStockDistributionChartData()
    {
        $analysis = $this->stockAnalysis;
        
        return [
            'series' => [
                $analysis['out_of_stock'] ?? 0,
                $analysis['low_stock'] ?? 0,
                $analysis['adequate_stock'] ?? 0,
                $analysis['overstocked'] ?? 0,
            ],
            'labels' => ['Out of Stock', 'Low Stock', 'Adequate Stock', 'Overstocked'],
            'colors' => ['#ef4444', '#f97316', '#22c55e', '#3b82f6'],
        ];
    }

    public function getMovementCategoryChartData()
    {
        $categories = collect($this->productMovement)
            ->groupBy('movement_category')
            ->map(function ($group) {
                return $group->count();
            });

        return [
            'series' => $categories->values()->toArray(),
            'labels' => $categories->keys()->toArray(),
        ];
    }

    public function getReorderPriorityData()
    {
        $priorities = collect($this->reorderRecommendations)
            ->groupBy('priority')
            ->map(function ($group) {
                return $group->count();
            });

        return [
            'critical' => $priorities->get('Critical', 0),
            'high' => $priorities->get('High', 0),
            'medium' => $priorities->get('Medium', 0),
            'low' => $priorities->get('Low', 0),
        ];
    }

    public function render()
    {
        return view('livewire.vendor.vendor-inventory-dashboard');
    }
}
