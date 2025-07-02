<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsService
{
    public function getComprehensiveAnalytics($dateRange = '30_days')
    {
        $dates = $this->getDateRange($dateRange);
        
        return [
            'revenue' => $this->getRevenueMetrics($dates),
            'orders' => $this->getOrderMetrics($dates),
            'users' => $this->getUserMetrics($dates),
            'inventory' => $this->getInventoryMetrics($dates),
            'performance' => $this->getPerformanceMetrics($dates)
        ];
    }
    
    public function getChartData($metric, $dateRange)
    {
        $dates = $this->getDateRange($dateRange);
        
        switch ($metric) {
            case 'revenue':
                return $this->getRevenueChartData($dates);
            case 'orders':
                return $this->getOrderChartData($dates);
            case 'users':
                return $this->getUserChartData($dates);
            case 'inventory':
                return $this->getInventoryChartData($dates);
            default:
                return [];
        }
    }
    
    public function getComparisonData($metric, $dateRange)
    {
        $currentDates = $this->getDateRange($dateRange);
        $previousDates = $this->getPreviousDateRange($dateRange);
        
        $currentData = $this->getChartData($metric, $dateRange);
        $previousData = $this->getMetricData($metric, $previousDates);
        
        return [
            'current' => array_sum($currentData['values'] ?? []),
            'previous' => array_sum($previousData),
            'change_percent' => $this->calculatePercentageChange(
                array_sum($previousData),
                array_sum($currentData['values'] ?? [])
            )
        ];
    }
    
    public function exportAnalytics($dateRange, $format = 'csv')
    {
        $analytics = $this->getComprehensiveAnalytics($dateRange);
        $fileName = 'analytics_export_' . date('Y-m-d_H-i-s') . '.' . $format;
        $filePath = storage_path('app/exports/' . $fileName);
        
        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        if ($format === 'csv') {
            $this->exportToCsv($analytics, $filePath);
        } else {
            $this->exportToJson($analytics, $filePath);
        }
        
        return $fileName;
    }
    
    public function generateReport($type, $dateRange)
    {
        $analytics = $this->getComprehensiveAnalytics($dateRange);
        $fileName = $type . '_report_' . date('Y-m-d_H-i-s') . '.pdf';
        $filePath = storage_path('app/reports/' . $fileName);
        
        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Generate PDF report (implementation would depend on PDF library)
        // For now, return a mock file path
        file_put_contents($filePath, json_encode($analytics, JSON_PRETTY_PRINT));
        
        return $fileName;
    }
    
    protected function getDateRange($range)
    {
        $endDate = Carbon::now();
        
        switch ($range) {
            case '7_days':
                $startDate = $endDate->copy()->subDays(7);
                break;
            case '30_days':
                $startDate = $endDate->copy()->subDays(30);
                break;
            case '90_days':
                $startDate = $endDate->copy()->subDays(90);
                break;
            case '1_year':
                $startDate = $endDate->copy()->subYear();
                break;
            default:
                $startDate = $endDate->copy()->subDays(30);
        }
        
        return [
            'start' => $startDate,
            'end' => $endDate
        ];
    }
    
    protected function getPreviousDateRange($range)
    {
        $current = $this->getDateRange($range);
        $diff = $current['end']->diffInDays($current['start']);
        
        return [
            'start' => $current['start']->copy()->subDays($diff),
            'end' => $current['start']
        ];
    }
    
    protected function getRevenueMetrics($dates)
    {
        $totalRevenue = Order::whereBetween('created_at', [$dates['start'], $dates['end']])
            ->where('status', 'completed')
            ->sum('total_amount');
        
        $previousRevenue = Order::whereBetween('created_at', [
                $dates['start']->copy()->subDays($dates['end']->diffInDays($dates['start'])),
                $dates['start']
            ])
            ->where('status', 'completed')
            ->sum('total_amount');
        
        return [
            'total' => $totalRevenue,
            'previous' => $previousRevenue,
            'change_percent' => $this->calculatePercentageChange($previousRevenue, $totalRevenue),
            'average_order_value' => $this->getAverageOrderValue($dates),
            'monthly_recurring' => $this->getMonthlyRecurringRevenue($dates)
        ];
    }
    
    protected function getOrderMetrics($dates)
    {
        $totalOrders = Order::whereBetween('created_at', [$dates['start'], $dates['end']])->count();
        $completedOrders = Order::whereBetween('created_at', [$dates['start'], $dates['end']])
            ->where('status', 'completed')->count();
        $pendingOrders = Order::whereBetween('created_at', [$dates['start'], $dates['end']])
            ->where('status', 'pending')->count();
        
        return [
            'total' => $totalOrders,
            'completed' => $completedOrders,
            'pending' => $pendingOrders,
            'completion_rate' => $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0,
            'daily_average' => $totalOrders / max(1, $dates['end']->diffInDays($dates['start']))
        ];
    }
    
    protected function getUserMetrics($dates)
    {
        $newUsers = User::whereBetween('created_at', [$dates['start'], $dates['end']])->count();
        $activeUsers = User::whereBetween('last_login', [$dates['start'], $dates['end']])->count();
        $totalUsers = User::count();
        
        return [
            'new' => $newUsers,
            'active' => $activeUsers,
            'total' => $totalUsers,
            'growth_rate' => $this->calculateGrowthRate($newUsers, $dates),
            'retention_rate' => $this->getUserRetentionRate($dates)
        ];
    }
    
    protected function getInventoryMetrics($dates)
    {
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)->count();
        $outOfStockProducts = Product::where('stock_quantity', 0)->count();
        
        return [
            'total_products' => $totalProducts,
            'low_stock' => $lowStockProducts,
            'out_of_stock' => $outOfStockProducts,
            'stock_value' => Product::sum(DB::raw('stock_quantity * price')),
            'turnover_rate' => $this->getInventoryTurnoverRate($dates)
        ];
    }
    
    protected function getPerformanceMetrics($dates)
    {
        return [
            'page_load_time' => rand(800, 1500) / 1000, // Simulated
            'uptime_percentage' => 99.9,
            'error_rate' => 0.1,
            'response_time' => rand(100, 300) / 1000,
            'throughput' => rand(100, 500)
        ];
    }
    
    protected function getRevenueChartData($dates)
    {
        $data = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
        
        return [
            'labels' => array_keys($data),
            'values' => array_values($data)
        ];
    }
    
    protected function getOrderChartData($dates)
    {
        $data = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
        
        return [
            'labels' => array_keys($data),
            'values' => array_values($data)
        ];
    }
    
    protected function getUserChartData($dates)
    {
        $data = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
        
        return [
            'labels' => array_keys($data),
            'values' => array_values($data)
        ];
    }
    
    protected function getInventoryChartData($dates)
    {
        // Mock inventory movement data
        $labels = [];
        $values = [];
        
        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = $date;
            $values[] = rand(50, 200);
        }
        
        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
    
    protected function getMetricData($metric, $dates)
    {
        switch ($metric) {
            case 'revenue':
                return [Order::whereBetween('created_at', [$dates['start'], $dates['end']])
                    ->where('status', 'completed')->sum('total_amount')];
            case 'orders':
                return [Order::whereBetween('created_at', [$dates['start'], $dates['end']])->count()];
            case 'users':
                return [User::whereBetween('created_at', [$dates['start'], $dates['end']])->count()];
            default:
                return [0];
        }
    }
    
    protected function calculatePercentageChange($old, $new)
    {
        if ($old == 0) return $new > 0 ? 100 : 0;
        return (($new - $old) / $old) * 100;
    }
    
    protected function calculateGrowthRate($newUsers, $dates)
    {
        $days = $dates['end']->diffInDays($dates['start']);
        return $days > 0 ? ($newUsers / $days) * 30 : 0; // Monthly growth rate
    }
    
    protected function getAverageOrderValue($dates)
    {
        return Order::whereBetween('created_at', [$dates['start'], $dates['end']])
            ->where('status', 'completed')
            ->avg('total_amount') ?? 0;
    }
    
    protected function getMonthlyRecurringRevenue($dates)
    {
        // Mock MRR calculation
        return rand(10000, 50000);
    }
    
    protected function getUserRetentionRate($dates)
    {
        // Mock retention rate calculation
        return rand(70, 90);
    }
    
    protected function getInventoryTurnoverRate($dates)
    {
        // Mock turnover rate calculation
        return rand(4, 12);
    }
    
    protected function exportToCsv($data, $filePath)
    {
        $handle = fopen($filePath, 'w');
        
        // Headers
        fputcsv($handle, ['Metric', 'Value', 'Previous', 'Change %']);
        
        // Revenue data
        fputcsv($handle, ['Total Revenue', $data['revenue']['total'], $data['revenue']['previous'], $data['revenue']['change_percent']]);
        fputcsv($handle, ['Average Order Value', $data['revenue']['average_order_value'], '', '']);
        
        // Order data
        fputcsv($handle, ['Total Orders', $data['orders']['total'], '', '']);
        fputcsv($handle, ['Completed Orders', $data['orders']['completed'], '', '']);
        fputcsv($handle, ['Completion Rate %', $data['orders']['completion_rate'], '', '']);
        
        // User data
        fputcsv($handle, ['New Users', $data['users']['new'], '', '']);
        fputcsv($handle, ['Active Users', $data['users']['active'], '', '']);
        fputcsv($handle, ['Total Users', $data['users']['total'], '', '']);
        
        fclose($handle);
    }
    
    protected function exportToJson($data, $filePath)
    {
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
