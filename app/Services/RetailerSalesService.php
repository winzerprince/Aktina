<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RetailerSalesService
{
    public function getSalesMetrics()
    {
        return Cache::remember('retailer_sales_metrics', 300, function () {
            $totalOrders = Order::where('user_id', auth()->id())->count();
            $totalRevenue = Order::where('user_id', auth()->id())->sum('total_amount');
            $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
            
            $thisMonthOrders = Order::where('user_id', auth()->id())
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
            
            $lastMonthOrders = Order::where('user_id', auth()->id())
                ->whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(), 
                    now()->subMonth()->endOfMonth()
                ])
                ->count();

            $orderGrowth = $lastMonthOrders > 0 
                ? (($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 
                : 0;

            return [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'average_order_value' => $avgOrderValue,
                'orders_this_month' => $thisMonthOrders,
                'order_growth_percentage' => round($orderGrowth, 1),
                'pending_orders' => Order::where('user_id', auth()->id())
                    ->where('status', 'pending')
                    ->count(),
                'completed_orders' => Order::where('user_id', auth()->id())
                    ->whereIn('status', ['delivered', 'completed'])
                    ->count(),
            ];
        });
    }

    public function getSalesTrends($months = 6)
    {
        return Cache::remember("retailer_sales_trends_{$months}m", 300, function () use ($months) {
            $data = [];
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthStart = $date->startOfMonth()->copy();
                $monthEnd = $date->endOfMonth()->copy();

                $orders = Order::where('user_id', auth()->id())
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();

                $revenue = Order::where('user_id', auth()->id())
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('total_amount');

                $data[] = [
                    'month' => $date->format('M Y'),
                    'orders' => $orders,
                    'revenue' => $revenue,
                    'average_order_value' => $orders > 0 ? $revenue / $orders : 0,
                ];
            }

            return $data;
        });
    }

    public function getTopPurchasedProducts($limit = 10)
    {
        return Cache::remember('retailer_top_products', 300, function () use ($limit) {
            return OrderItem::whereHas('order', function ($query) {
                $query->where('user_id', auth()->id());
            })
                ->with('product')
                ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(quantity * price) as total_spent'))
                ->groupBy('product_id')
                ->orderByDesc('total_quantity')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Unknown Product',
                        'product_sku' => $item->product->sku ?? '',
                        'total_quantity' => $item->total_quantity,
                        'total_spent' => $item->total_spent,
                        'average_price' => $item->total_quantity > 0 ? $item->total_spent / $item->total_quantity : 0,
                    ];
                });
        });
    }

    public function getPurchasePatterns()
    {
        return Cache::remember('retailer_purchase_patterns', 300, function () {
            // Get purchase frequency by day of week
            $dayPatterns = Order::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subMonths(3))
                ->select(DB::raw('DAYOFWEEK(created_at) as day_of_week'), DB::raw('COUNT(*) as order_count'))
                ->groupBy('day_of_week')
                ->get()
                ->mapWithKeys(function ($item) {
                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    return [$days[$item->day_of_week - 1] => $item->order_count];
                });

            // Get purchase frequency by hour
            $hourPatterns = Order::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subMonths(1))
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as order_count'))
                ->groupBy('hour')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->hour . ':00' => $item->order_count];
                });

            // Get average time between orders
            $orders = Order::where('user_id', auth()->id())
                ->orderBy('created_at')
                ->pluck('created_at')
                ->toArray();

            $timeBetweenOrders = [];
            for ($i = 1; $i < count($orders); $i++) {
                $diff = strtotime($orders[$i]) - strtotime($orders[$i - 1]);
                $timeBetweenOrders[] = $diff / (24 * 60 * 60); // Convert to days
            }

            $avgTimeBetweenOrders = count($timeBetweenOrders) > 0 
                ? array_sum($timeBetweenOrders) / count($timeBetweenOrders) 
                : 0;

            return [
                'day_patterns' => $dayPatterns->toArray(),
                'hour_patterns' => $hourPatterns->toArray(),
                'average_days_between_orders' => round($avgTimeBetweenOrders, 1),
                'purchase_frequency_category' => $this->categorizePurchaseFrequency($avgTimeBetweenOrders),
            ];
        });
    }

    public function getOrderStatusBreakdown()
    {
        return Cache::remember('retailer_order_status', 300, function () {
            return Order::where('user_id', auth()->id())
                ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total_value'))
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->status => [
                            'count' => $item->count,
                            'total_value' => $item->total_value,
                            'percentage' => 0, // Will be calculated later
                        ]
                    ];
                });
        });
    }

    public function getRecentOrderActivity($limit = 10)
    {
        return Order::where('user_id', auth()->id())
            ->with(['orderItems.product'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'items_count' => $order->orderItems->count(),
                    'created_at' => $order->created_at,
                    'items_preview' => $order->orderItems->take(3)->pluck('product.name')->implode(', '),
                ];
            });
    }

    public function getSeasonalTrends()
    {
        return Cache::remember('retailer_seasonal_trends', 300, function () {
            $quarters = [];
            for ($i = 3; $i >= 0; $i--) {
                $quarter = now()->subQuarters($i);
                $quarterStart = $quarter->copy()->startOfQuarter();
                $quarterEnd = $quarter->copy()->endOfQuarter();

                $orders = Order::where('user_id', auth()->id())
                    ->whereBetween('created_at', [$quarterStart, $quarterEnd])
                    ->count();

                $revenue = Order::where('user_id', auth()->id())
                    ->whereBetween('created_at', [$quarterStart, $quarterEnd])
                    ->sum('total_amount');

                $quarters[] = [
                    'quarter' => 'Q' . $quarter->quarter . ' ' . $quarter->year,
                    'orders' => $orders,
                    'revenue' => $revenue,
                ];
            }

            return $quarters;
        });
    }

    private function categorizePurchaseFrequency($avgDays)
    {
        if ($avgDays <= 7) return 'Very Frequent';
        if ($avgDays <= 14) return 'Frequent';
        if ($avgDays <= 30) return 'Regular';
        if ($avgDays <= 60) return 'Occasional';
        return 'Infrequent';
    }
}
