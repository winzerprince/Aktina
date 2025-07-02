<?php

namespace App\Services;

use App\Interfaces\Repositories\AnalyticsRepositoryInterface;
use App\Interfaces\Services\AnalyticsServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AnalyticsService implements AnalyticsServiceInterface
{
    protected $analyticsRepository;

    public function __construct(AnalyticsRepositoryInterface $analyticsRepository)
    {
        $this->analyticsRepository = $analyticsRepository;
    }

    public function getDashboardData(User $user, string $period = '30days')
    {
        $cacheKey = "dashboard_data_{$user->id}_{$period}";
        
        return $this->getCachedAnalyticsData($cacheKey) ?: $this->cacheAnalyticsData($cacheKey, function () use ($user, $period) {
            $data = [];
            
            // Role-specific dashboard data
            switch ($user->role) {
                case 'admin':
                    $data = $this->getAdminDashboardData($period);
                    break;
                case 'production_manager':
                    $data = $this->getProductionManagerDashboardData($period);
                    break;
                case 'vendor':
                    $data = $this->getVendorDashboardData($user, $period);
                    break;
                case 'retailer':
                    $data = $this->getRetailerDashboardData($user, $period);
                    break;
                case 'supplier':
                    $data = $this->getSupplierDashboardData($user, $period);
                    break;
                case 'hr_manager':
                    $data = $this->getHRManagerDashboardData($period);
                    break;
            }
            
            return $data;
        });
    }
    
    public function getSalesTrends(User $user = null, string $period = '30days')
    {
        $days = (int) filter_var($period, FILTER_SANITIZE_NUMBER_INT);
        $startDate = now()->subDays($days)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $salesData = $this->analyticsRepository->getSalesAnalytics($user?->id, $startDate, $endDate);
        
        return $this->generateChartData('line', $salesData->map(function ($item) {
            return [
                'date' => $item->date->format('Y-m-d'),
                'revenue' => $item->revenue,
                'orders' => $item->orders_count,
                'customers' => $item->customers_count,
            ];
        })->toArray());
    }
    
    public function getInventoryAnalytics(int $warehouseId = null, string $period = '30days')
    {
        $inventoryData = $this->analyticsRepository->getInventoryTrends($warehouseId, $period);
        
        return [
            'low_stock_count' => $inventoryData->where('available_quantity', '<=', 'reorder_level')->count(),
            'overstock_count' => $inventoryData->where('units', '>=', 'overstock_level')->count(),
            'total_items' => $inventoryData->count(),
            'stock_levels' => $inventoryData->map(function ($item) {
                return [
                    'name' => $item->name,
                    'current' => $item->available_quantity,
                    'reorder_level' => $item->reorder_level,
                    'status' => $this->getStockStatus($item),
                ];
            }),
        ];
    }
    
    public function getProductionMetrics(string $period = '30days')
    {
        $days = (int) filter_var($period, FILTER_SANITIZE_NUMBER_INT);
        $startDate = now()->subDays($days)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        return $this->analyticsRepository->getProductionMetrics($startDate, $endDate);
    }
    
    public function getSystemMetrics(string $period = '30days')
    {
        $days = (int) filter_var($period, FILTER_SANITIZE_NUMBER_INT);
        $startDate = now()->subDays($days)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        return $this->analyticsRepository->getSystemMetrics($startDate, $endDate);
    }
    
    public function getUserGrowthData(string $period = '30days')
    {
        return $this->analyticsRepository->getUserActivityData($period);
    }
    
    public function getOrderAnalytics(User $user = null, string $period = '30days')
    {
        return $this->analyticsRepository->getOrderTrends($user?->id, $period);
    }
    
    public function generateChartData(string $type, array $data, array $options = [])
    {
        return [
            'chart' => [
                'type' => $type,
                'height' => $options['height'] ?? 300,
                'toolbar' => ['show' => false],
            ],
            'series' => $this->formatSeriesData($data, $options),
            'xaxis' => [
                'categories' => $this->extractCategories($data, $options['x_field'] ?? 'date'),
            ],
            'colors' => $options['colors'] ?? ['#3B82F6', '#10B981', '#F59E0B'],
            'dataLabels' => ['enabled' => $options['show_labels'] ?? false],
            'stroke' => ['curve' => 'smooth'],
        ];
    }
    
    public function cacheAnalyticsData(string $key, $data, int $ttl = 3600)
    {
        if (is_callable($data)) {
            $data = $data();
        }
        
        Cache::put($key, $data, $ttl);
        return $data;
    }
    
    public function getCachedAnalyticsData(string $key)
    {
        return Cache::get($key);
    }
    
    private function getAdminDashboardData(string $period)
    {
        return [
            'sales_trends' => $this->getSalesTrends(null, $period),
            'inventory_summary' => $this->getInventoryAnalytics(null, $period),
            'system_metrics' => $this->getSystemMetrics($period),
            'user_growth' => $this->getUserGrowthData($period),
        ];
    }
    
    private function getProductionManagerDashboardData(string $period)
    {
        return [
            'production_metrics' => $this->getProductionMetrics($period),
            'inventory_analytics' => $this->getInventoryAnalytics(null, $period),
            'resource_usage' => [], // To be implemented
        ];
    }
    
    private function getVendorDashboardData(User $user, string $period)
    {
        return [
            'sales_performance' => $this->getSalesTrends($user, $period),
            'order_analytics' => $this->getOrderAnalytics($user, $period),
        ];
    }
    
    private function getRetailerDashboardData(User $user, string $period)
    {
        return [
            'sales_trends' => $this->getSalesTrends($user, $period),
            'customer_analytics' => [], // To be implemented
        ];
    }
    
    private function getSupplierDashboardData(User $user, string $period)
    {
        return [
            'order_stats' => $this->getOrderAnalytics($user, $period),
            'delivery_metrics' => [], // To be implemented
        ];
    }
    
    private function getHRManagerDashboardData(string $period)
    {
        return [
            'workforce_analytics' => [], // To be implemented
            'performance_metrics' => [], // To be implemented
        ];
    }
    
    private function getStockStatus($item)
    {
        if ($item->available_quantity <= ($item->reorder_level * 0.5)) {
            return 'critical';
        } elseif ($item->available_quantity <= $item->reorder_level) {
            return 'low';
        } elseif ($item->units >= $item->overstock_level) {
            return 'overstock';
        }
        return 'normal';
    }
    
    private function formatSeriesData(array $data, array $options)
    {
        // Basic implementation - can be enhanced for different chart types
        $yFields = $options['y_fields'] ?? ['value'];
        $series = [];
        
        foreach ($yFields as $field) {
            $series[] = [
                'name' => ucfirst(str_replace('_', ' ', $field)),
                'data' => array_column($data, $field),
            ];
        }
        
        return $series;
    }
    
    private function extractCategories(array $data, string $field)
    {
        return array_column($data, $field);
    }
}
