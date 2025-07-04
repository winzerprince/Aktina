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
            
            return Order::where('seller_id', $vendorId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw("DATE_FORMAT(created_at, '{$groupBy}') as period")
                ->selectRaw('SUM(price) as revenue')
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
        
        return Order::join('users', 'orders.buyer_id', '=', 'users.id')
            ->where('orders.seller_id', $vendorId)
            ->where('orders.created_at', '>=', $startDate)
            ->where('users.role', 'retailer')
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('SUM(orders.price) as total_spent')
            ->selectRaw('COUNT(orders.id) as order_count')
            ->selectRaw('AVG(orders.price) as average_order')
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
        
        $orders = Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->get();
        
        $productRevenues = [];
        
        foreach ($orders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            
            if (!is_array($items)) {
                continue;
            }
            
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? 0;
                $unitPrice = $item['unit_price'] ?? 0;
                
                if ($productId) {
                    if (!isset($productRevenues[$productId])) {
                        // Get product name from database
                        $product = \App\Models\Product::find($productId);
                        $productRevenues[$productId] = [
                            'product_id' => $productId,
                            'name' => $product ? $product->name : 'Unknown Product',
                            'revenue' => 0,
                            'quantity_sold' => 0,
                        ];
                    }
                    
                    $productRevenues[$productId]['revenue'] += $quantity * $unitPrice;
                    $productRevenues[$productId]['quantity_sold'] += $quantity;
                }
            }
        }
        
        // Sort by revenue desc
        usort($productRevenues, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
        
        return collect($productRevenues)->map(function ($product) {
            return [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'revenue' => (float) $product['revenue'],
                'quantity_sold' => (int) $product['quantity_sold'],
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

    public function getTotalRevenue($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->sum('price') ?? 0;
    }

    private function getOrderCount($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    public function getAverageOrderValue($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->avg('price') ?? 0;
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
        $orders = Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->get();
        
        $productSales = [];
        
        foreach ($orders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            
            if (!is_array($items)) {
                continue;
            }
            
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? 0;
                
                if ($productId) {
                    if (!isset($productSales[$productId])) {
                        // Get product name from database
                        $product = \App\Models\Product::find($productId);
                        $productSales[$productId] = [
                            'name' => $product ? $product->name : 'Unknown Product',
                            'total_sold' => 0,
                        ];
                    }
                    
                    $productSales[$productId]['total_sold'] += $quantity;
                }
            }
        }
        
        // Sort by total sold desc and take top N
        arsort($productSales);
        $topProducts = array_slice($productSales, 0, $limit, true);
        
        return array_map(function($product) {
            return $product['total_sold'];
        }, $topProducts);
    }

    private function getConversionRate($vendorId, $startDate)
    {
        // Mock implementation - would need to track visitors/leads
        return rand(15, 35) + (rand(0, 99) / 100);
    }

    private function getTotalActiveRetailers($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->distinct('buyer_id')
            ->count('buyer_id');
    }

    private function getNewRetailers($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->whereNotExists(function ($query) use ($vendorId, $startDate) {
                $query->select(DB::raw(1))
                    ->from('orders as o2')
                    ->whereColumn('o2.buyer_id', 'orders.buyer_id')
                    ->where('o2.seller_id', $vendorId)
                    ->where('o2.created_at', '<', $startDate);
            })
            ->distinct('buyer_id')
            ->count('buyer_id');
    }

    private function getRepeatCustomers($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->select('buyer_id')
            ->groupBy('buyer_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
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
        $daysDiff = abs(intval(Carbon::now()->diffInDays($startDate)));
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
