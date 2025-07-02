<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Retailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VendorSalesService
{
    public function getSalesMetrics($vendorId, $timeframe = '30d')
    {
        $cacheKey = "vendor_sales_metrics_{$vendorId}_{$timeframe}";
        
        return Cache::remember($cacheKey, 300, function () use ($vendorId, $timeframe) {
            $startDate = $this->getTimeframeDate($timeframe);
            
            return [
                'total_revenue' => $this->getTotalRevenue($vendorId, $startDate),
                'order_count' => $this->getOrderCount($vendorId, $startDate),
                'average_order_value' => $this->getAverageOrderValue($vendorId, $startDate),
                'growth_rate' => $this->getGrowthRate($vendorId, $startDate),
                'top_products' => $this->getTopProducts($vendorId, $startDate),
                'conversion_rate' => $this->getConversionRate($vendorId, $startDate),
            ];
        });
    }

    public function getSalesTrend($vendorId, $timeframe = '30d')
    {
        $cacheKey = "vendor_sales_trend_{$vendorId}_{$timeframe}";
        
        return Cache::remember($cacheKey, 300, function () use ($vendorId, $timeframe) {
            $startDate = $this->getTimeframeDate($timeframe);
            $groupBy = $this->getGroupByFormat($timeframe);
            
            return Order::where('vendor_id', $vendorId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw("DATE_FORMAT(created_at, '{$groupBy}') as period")
                ->selectRaw('SUM(total_amount) as revenue')
                ->selectRaw('COUNT(*) as order_count')
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->period => [
                        'revenue' => (float) $item->revenue,
                        'orders' => (int) $item->order_count
                    ]];
                });
        });
    }

    public function getTopRetailers($vendorId, $timeframe = '30d', $limit = 10)
    {
        $startDate = $this->getTimeframeDate($timeframe);
        
        return Order::join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.vendor_id', $vendorId)
            ->where('orders.created_at', '>=', $startDate)
            ->where('users.role', 'retailer')
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('SUM(orders.total_amount) as total_spent')
            ->selectRaw('COUNT(orders.id) as order_count')
            ->selectRaw('AVG(orders.total_amount) as average_order')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(function ($retailer) {
                return [
                    'id' => $retailer->id,
                    'name' => $retailer->name,
                    'email' => $retailer->email,
                    'total_spent' => (float) $retailer->total_spent,
                    'order_count' => (int) $retailer->order_count,
                    'average_order' => (float) $retailer->average_order,
                ];
            });
    }

    public function getRevenueByProduct($vendorId, $timeframe = '30d')
    {
        $startDate = $this->getTimeframeDate($timeframe);
        
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.vendor_id', $vendorId)
            ->where('orders.created_at', '>=', $startDate)
            ->select('products.name', 'products.id')
            ->selectRaw('SUM(order_items.quantity * order_items.unit_price) as revenue')
            ->selectRaw('SUM(order_items.quantity) as quantity_sold')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'revenue' => (float) $product->revenue,
                    'quantity_sold' => (int) $product->quantity_sold,
                ];
            });
    }

    public function getSalesGoalProgress($vendorId, $goalAmount, $timeframe = '30d')
    {
        $currentRevenue = $this->getTotalRevenue($vendorId, $this->getTimeframeDate($timeframe));
        $progressPercentage = $goalAmount > 0 ? ($currentRevenue / $goalAmount) * 100 : 0;
        
        return [
            'goal_amount' => $goalAmount,
            'current_revenue' => $currentRevenue,
            'progress_percentage' => min(100, $progressPercentage),
            'remaining_amount' => max(0, $goalAmount - $currentRevenue),
        ];
    }

    public function getRetailerPerformanceMetrics($vendorId, $timeframe = '30d')
    {
        $startDate = $this->getTimeframeDate($timeframe);
        
        return [
            'total_retailers' => $this->getTotalActiveRetailers($vendorId, $startDate),
            'new_retailers' => $this->getNewRetailers($vendorId, $startDate),
            'repeat_customers' => $this->getRepeatCustomers($vendorId, $startDate),
            'churn_rate' => $this->getChurnRate($vendorId, $startDate),
            'retailer_satisfaction' => $this->getRetailerSatisfactionScore($vendorId, $startDate),
        ];
    }

    private function getTotalRevenue($vendorId, $startDate)
    {
        return Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->sum('total_amount') ?? 0;
    }

    private function getOrderCount($vendorId, $startDate)
    {
        return Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    private function getAverageOrderValue($vendorId, $startDate)
    {
        return Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->avg('total_amount') ?? 0;
    }

    private function getGrowthRate($vendorId, $startDate)
    {
        $currentPeriodRevenue = $this->getTotalRevenue($vendorId, $startDate);
        $previousPeriodRevenue = $this->getTotalRevenue($vendorId, $this->getPreviousPeriodDate($startDate));
        
        if ($previousPeriodRevenue == 0) {
            return $currentPeriodRevenue > 0 ? 100 : 0;
        }
        
        return (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100;
    }

    private function getTopProducts($vendorId, $startDate, $limit = 5)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.vendor_id', $vendorId)
            ->where('orders.created_at', '>=', $startDate)
            ->select('products.name')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->pluck('total_sold', 'name')
            ->toArray();
    }

    private function getConversionRate($vendorId, $startDate)
    {
        // Mock implementation - would need to track visitors/leads
        return rand(15, 35) + (rand(0, 99) / 100);
    }

    private function getTotalActiveRetailers($vendorId, $startDate)
    {
        return Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->distinct('user_id')
            ->count('user_id');
    }

    private function getNewRetailers($vendorId, $startDate)
    {
        return Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->whereNotExists(function ($query) use ($vendorId, $startDate) {
                $query->select(DB::raw(1))
                    ->from('orders as o2')
                    ->whereColumn('o2.user_id', 'orders.user_id')
                    ->where('o2.vendor_id', $vendorId)
                    ->where('o2.created_at', '<', $startDate);
            })
            ->distinct('user_id')
            ->count('user_id');
    }

    private function getRepeatCustomers($vendorId, $startDate)
    {
        return Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();
    }

    private function getChurnRate($vendorId, $startDate)
    {
        // Mock implementation - would need proper churn calculation
        return rand(5, 15) + (rand(0, 99) / 100);
    }

    private function getRetailerSatisfactionScore($vendorId, $startDate)
    {
        // Mock implementation - would integrate with review/rating system
        return rand(75, 95) + (rand(0, 99) / 100);
    }

    private function getTimeframeDate($timeframe)
    {
        return match($timeframe) {
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            '90d' => Carbon::now()->subDays(90),
            '1y' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(30),
        };
    }

    private function getPreviousPeriodDate($startDate)
    {
        $daysDiff = Carbon::now()->diffInDays($startDate);
        return Carbon::now()->subDays($daysDiff * 2);
    }

    private function getGroupByFormat($timeframe)
    {
        return match($timeframe) {
            '7d' => '%Y-%m-%d',
            '30d' => '%Y-%m-%d',
            '90d' => '%Y-%m-%d',
            '1y' => '%Y-%m',
            default => '%Y-%m-%d',
        };
    }
}
