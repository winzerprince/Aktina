<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdvancedAnalyticsService
{
    public function getRevenueAnalytics(string $timeRange = '30d'): array
    {
        return Cache::remember("revenue_analytics_{$timeRange}", 300, function () use ($timeRange) {
            $startDate = $this->getStartDate($timeRange);
            
            $data = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            return [
                'series' => [
                    [
                        'name' => 'Revenue',
                        'data' => $data->pluck('revenue')->toArray()
                    ],
                    [
                        'name' => 'Orders',
                        'data' => $data->pluck('order_count')->toArray()
                    ],
                    [
                        'name' => 'Avg Order Value',
                        'data' => $data->pluck('avg_order_value')->toArray()
                    ]
                ],
                'categories' => $data->pluck('date')->map(function ($date) {
                    return Carbon::parse($date)->format('M d');
                })->toArray(),
                'totals' => [
                    'revenue' => $data->sum('revenue'),
                    'orders' => $data->sum('order_count'),
                    'avg_order_value' => $data->avg('avg_order_value')
                ]
            ];
        });
    }

    public function getOrderTrends(string $timeRange = '30d'): array
    {
        return Cache::remember("order_trends_{$timeRange}", 300, function () use ($timeRange) {
            $startDate = $this->getStartDate($timeRange);
            
            $statusData = Order::select('status', DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('status')
                ->get();

            $timeData = Order::select(
                DB::raw('DATE(created_at) as date'),
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

            return [
                'status_breakdown' => $statusData->map(function ($item) {
                    return [
                        'status' => ucfirst($item->status),
                        'count' => $item->count
                    ];
                })->toArray(),
                'trend_data' => $this->formatTimeSeriesData($timeData)
            ];
        });
    }

    public function getUserGrowthData(string $timeRange = '30d'): array
    {
        return Cache::remember("user_growth_{$timeRange}", 300, function () use ($timeRange) {
            $startDate = $this->getStartDate($timeRange);
            
            $data = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as new_users')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            $cumulativeData = [];
            $total = 0;
            
            foreach ($data as $item) {
                $total += $item->new_users;
                $cumulativeData[] = $total;
            }

            return [
                'series' => [
                    [
                        'name' => 'New Users',
                        'data' => $data->pluck('new_users')->toArray()
                    ],
                    [
                        'name' => 'Total Users',
                        'data' => $cumulativeData
                    ]
                ],
                'categories' => $data->pluck('date')->map(function ($date) {
                    return Carbon::parse($date)->format('M d');
                })->toArray()
            ];
        });
    }

    public function getInventoryMetrics(): array
    {
        return Cache::remember('inventory_metrics', 600, function () {
            $totalProducts = Product::count();
            $lowStock = Product::where('stock_quantity', '<=', 10)->count();
            $outOfStock = Product::where('stock_quantity', 0)->count();
            $totalValue = Product::sum(DB::raw('stock_quantity * price'));

            $categoryBreakdown = Product::select('category_id', DB::raw('COUNT(*) as count'))
                ->with('category:id,name')
                ->groupBy('category_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'category' => $item->category->name ?? 'Uncategorized',
                        'count' => $item->count
                    ];
                });

            return [
                'summary' => [
                    'total_products' => $totalProducts,
                    'low_stock' => $lowStock,
                    'out_of_stock' => $outOfStock,
                    'total_value' => $totalValue,
                    'low_stock_percentage' => $totalProducts > 0 ? round(($lowStock / $totalProducts) * 100, 2) : 0
                ],
                'category_breakdown' => $categoryBreakdown->toArray()
            ];
        });
    }

    public function getPerformanceMetrics(): array
    {
        return Cache::remember('performance_metrics', 300, function () {
            $avgResponseTime = $this->calculateAverageResponseTime();
            $systemLoad = sys_getloadavg()[0] ?? 0;
            $memoryUsage = memory_get_usage(true);
            $peakMemory = memory_get_peak_usage(true);

            return [
                'response_time' => $avgResponseTime,
                'system_load' => $systemLoad,
                'memory_usage' => $this->formatBytes($memoryUsage),
                'peak_memory' => $this->formatBytes($peakMemory),
                'cache_hit_rate' => $this->getCacheHitRate(),
                'database_connections' => $this->getActiveConnections()
            ];
        });
    }

    public function getSalesHeatmapData(string $timeRange = '30d'): array
    {
        return Cache::remember("sales_heatmap_{$timeRange}", 600, function () use ($timeRange) {
            $startDate = $this->getStartDate($timeRange);
            
            $data = Order::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->groupBy('hour', 'day_of_week')
            ->get();

            $heatmapData = [];
            $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            
            for ($day = 1; $day <= 7; $day++) {
                for ($hour = 0; $hour < 24; $hour++) {
                    $item = $data->where('day_of_week', $day)->where('hour', $hour)->first();
                    $heatmapData[] = [
                        'x' => $hour,
                        'y' => $days[$day - 1],
                        'v' => $item ? $item->order_count : 0
                    ];
                }
            }

            return $heatmapData;
        });
    }

    public function getTopPerformingProducts(string $timeRange = '30d', int $limit = 10): array
    {
        return Cache::remember("top_products_{$timeRange}_{$limit}", 600, function () use ($timeRange, $limit) {
            $startDate = $this->getStartDate($timeRange);
            
            return DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'products.name',
                    'products.sku',
                    DB::raw('SUM(order_items.quantity) as total_sold'),
                    DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
                )
                ->where('orders.created_at', '>=', $startDate)
                ->where('orders.status', 'completed')
                ->groupBy('products.id', 'products.name', 'products.sku')
                ->orderBy('total_revenue', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getCustomerSegmentation(): array
    {
        return Cache::remember('customer_segmentation', 1800, function () {
            $segments = [
                'new' => User::where('created_at', '>=', now()->subDays(30))->count(),
                'active' => User::whereHas('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subDays(30));
                })->count(),
                'inactive' => User::whereDoesntHave('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subDays(90));
                })->count(),
                'vip' => User::whereHas('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subYear())
                        ->havingRaw('SUM(total_amount) > 1000');
                })->count()
            ];

            return $segments;
        });
    }

    public function exportAnalyticsData(string $timeRange, array $metrics): array
    {
        $data = [];
        
        if (in_array('revenue', $metrics)) {
            $data['revenue'] = $this->getRevenueAnalytics($timeRange);
        }
        
        if (in_array('orders', $metrics)) {
            $data['orders'] = $this->getOrderTrends($timeRange);
        }
        
        if (in_array('users', $metrics)) {
            $data['users'] = $this->getUserGrowthData($timeRange);
        }
        
        if (in_array('inventory', $metrics)) {
            $data['inventory'] = $this->getInventoryMetrics();
        }

        return $data;
    }

    private function getStartDate(string $timeRange): Carbon
    {
        return match ($timeRange) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30)
        };
    }

    private function formatTimeSeriesData($timeData): array
    {
        $grouped = $timeData->groupBy('status');
        $result = [];
        
        foreach ($grouped as $status => $data) {
            $result[] = [
                'name' => ucfirst($status),
                'data' => $data->pluck('count')->toArray()
            ];
        }
        
        return $result;
    }

    private function calculateAverageResponseTime(): float
    {
        // Simplified calculation - in real app, use APM tools
        return round(rand(100, 500) / 100, 2);
    }

    private function formatBytes(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    private function getCacheHitRate(): float
    {
        // Simplified calculation - integrate with your cache driver's stats
        return round(rand(85, 98), 2);
    }

    private function getActiveConnections(): int
    {
        try {
            return DB::select('SHOW STATUS LIKE "Threads_connected"')[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
