<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RetailerSalesService
{
    public function getSalesMetrics()
    {
        return Cache::remember('retailer_sales_metrics', 300, function () {
            $totalOrders = Order::where('buyer_id', auth()->id())->count();
            $totalRevenue = Order::where('buyer_id', auth()->id())->sum('price');
            $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
            
            $thisMonthOrders = Order::where('buyer_id', auth()->id())
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
            
            $lastMonthOrders = Order::where('buyer_id', auth()->id())
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
                'pending_orders' => Order::where('buyer_id', auth()->id())
                    ->where('status', 'pending')
                    ->count(),
                'completed_orders' => Order::where('buyer_id', auth()->id())
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

                $orders = Order::where('buyer_id', auth()->id())
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();

                $revenue = Order::where('buyer_id', auth()->id())
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('price');

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
            $orders = Order::where('buyer_id', auth()->id())
                ->get();

            $productStats = [];
            
            foreach ($orders as $order) {
                $items = $order->getItemsAsArray();
                foreach ($items as $item) {
                    $productId = $item['product_id'] ?? null;
                    $quantity = $item['quantity'] ?? 0;
                    $price = $item['price'] ?? 0;
                    
                    if ($productId) {
                        if (!isset($productStats[$productId])) {
                            $productStats[$productId] = [
                                'product_id' => $productId,
                                'total_quantity' => 0,
                                'total_spent' => 0,
                            ];
                        }
                        
                        $productStats[$productId]['total_quantity'] += $quantity;
                        $productStats[$productId]['total_spent'] += ($quantity * $price);
                    }
                }
            }

            // Sort by total quantity and limit
            $sortedProducts = collect($productStats)
                ->sortByDesc('total_quantity')
                ->take($limit)
                ->map(function ($item) {
                    $product = Product::find($item['product_id']);
                    return [
                        'product_id' => $item['product_id'],
                        'product_name' => $product->name ?? 'Unknown Product',
                        'product_sku' => $product->sku ?? '',
                        'total_quantity' => $item['total_quantity'],
                        'total_spent' => $item['total_spent'],
                        'average_price' => $item['total_quantity'] > 0 ? $item['total_spent'] / $item['total_quantity'] : 0,
                    ];
                });

            return $sortedProducts;
        });
    }

    public function getPurchasePatterns()
    {
        return Cache::remember('retailer_purchase_patterns', 300, function () {
            // Get purchase frequency by day of week
            $dayPatterns = Order::where('buyer_id', auth()->id())
                ->where('created_at', '>=', now()->subMonths(3))
                ->select(DB::raw('DAYOFWEEK(created_at) as day_of_week'), DB::raw('COUNT(*) as order_count'))
                ->groupBy('day_of_week')
                ->get()
                ->mapWithKeys(function ($item) {
                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    return [$days[$item->day_of_week - 1] => $item->order_count];
                });

            // Get purchase frequency by hour
            $hourPatterns = Order::where('buyer_id', auth()->id())
                ->where('created_at', '>=', now()->subMonths(1))
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as order_count'))
                ->groupBy('hour')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->hour . ':00' => $item->order_count];
                });

            // Get average time between orders
            $orders = Order::where('buyer_id', auth()->id())
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
            return Order::where('buyer_id', auth()->id())
                ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(price) as total_value'))
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
        return Order::where('buyer_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                $items = $order->getItemsAsArray();
                $itemsPreview = [];
                
                foreach (array_slice($items, 0, 3) as $item) {
                    $productId = $item['product_id'] ?? null;
                    if ($productId) {
                        $product = Product::find($productId);
                        if ($product) {
                            $itemsPreview[] = $product->name;
                        }
                    }
                }
                
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total_amount' => $order->price,
                    'items_count' => count($items),
                    'created_at' => $order->created_at,
                    'items_preview' => implode(', ', $itemsPreview),
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

                $orders = Order::where('buyer_id', auth()->id())
                    ->whereBetween('created_at', [$quarterStart, $quarterEnd])
                    ->count();

                $revenue = Order::where('buyer_id', auth()->id())
                    ->whereBetween('created_at', [$quarterStart, $quarterEnd])
                    ->sum('price');

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
