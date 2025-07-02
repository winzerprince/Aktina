<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class VendorInventoryService
{
    public function getInventoryTurnoverMetrics()
    {
        return Cache::remember('vendor_inventory_turnover', 300, function () {
            $totalProducts = Product::count();
            $activeProducts = Product::whereHas('orderItems', function ($query) {
                $query->whereHas('order', function ($orderQuery) {
                    $orderQuery->where('created_at', '>=', now()->subDays(30));
                });
            })->count();

            $avgTurnoverRate = $this->calculateAverageTurnoverRate();
            $fastMovingProducts = $this->getFastMovingProductsCount();
            $slowMovingProducts = $this->getSlowMovingProductsCount();

            return [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'active_percentage' => $totalProducts > 0 ? ($activeProducts / $totalProducts) * 100 : 0,
                'average_turnover_rate' => $avgTurnoverRate,
                'fast_moving_products' => $fastMovingProducts,
                'slow_moving_products' => $slowMovingProducts,
                'inventory_health_score' => $this->calculateInventoryHealthScore(),
            ];
        });
    }

    public function getProductMovementData($limit = 20)
    {
        return Cache::remember('product_movement_data', 300, function () use ($limit) {
            return Product::with(['orderItems' => function ($query) {
                $query->whereHas('order', function ($orderQuery) {
                    $orderQuery->where('created_at', '>=', now()->subDays(90));
                });
            }])
                ->get()
                ->map(function ($product) {
                    $totalSold = $product->orderItems->sum('quantity');
                    $totalRevenue = $product->orderItems->sum(function ($item) {
                        return $item->quantity * $item->price;
                    });
                    $daysInPeriod = 90;
                    $turnoverRate = $product->stock_quantity > 0 
                        ? ($totalSold / $product->stock_quantity) * ($daysInPeriod / 30)
                        : 0;

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'current_stock' => $product->stock_quantity,
                        'total_sold' => $totalSold,
                        'total_revenue' => $totalRevenue,
                        'turnover_rate' => round($turnoverRate, 2),
                        'days_of_stock' => $totalSold > 0 
                            ? round(($product->stock_quantity / $totalSold) * $daysInPeriod, 1)
                            : null,
                        'movement_category' => $this->categorizeMovement($turnoverRate),
                        'reorder_status' => $this->getReorderStatus($product, $turnoverRate),
                    ];
                })
                ->sortByDesc('turnover_rate')
                ->take($limit)
                ->values();
        });
    }

    public function getInventoryTurnoverTrends($months = 6)
    {
        return Cache::remember("inventory_turnover_trends_{$months}m", 300, function () use ($months) {
            $data = [];
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthStart = $date->startOfMonth()->copy();
                $monthEnd = $date->endOfMonth()->copy();

                $totalSold = OrderItem::whereHas('order', function ($query) use ($monthStart, $monthEnd) {
                    $query->whereBetween('created_at', [$monthStart, $monthEnd]);
                })->sum('quantity');

                $totalRevenue = OrderItem::whereHas('order', function ($query) use ($monthStart, $monthEnd) {
                    $query->whereBetween('created_at', [$monthStart, $monthEnd]);
                })->sum(DB::raw('quantity * price'));

                $avgInventoryValue = Product::avg(DB::raw('stock_quantity * price'));

                $data[] = [
                    'month' => $date->format('M Y'),
                    'total_sold' => $totalSold,
                    'total_revenue' => $totalRevenue,
                    'avg_inventory_value' => $avgInventoryValue,
                    'turnover_ratio' => $avgInventoryValue > 0 ? $totalRevenue / $avgInventoryValue : 0,
                ];
            }

            return $data;
        });
    }

    public function getStockLevelAnalysis()
    {
        return Cache::remember('stock_level_analysis', 300, function () {
            $products = Product::all();
            
            $analysis = [
                'out_of_stock' => 0,
                'low_stock' => 0,
                'adequate_stock' => 0,
                'overstocked' => 0,
                'total_value' => 0,
                'categories' => [],
            ];

            foreach ($products as $product) {
                $stockStatus = $this->getStockStatus($product);
                $analysis[$stockStatus]++;
                $analysis['total_value'] += $product->stock_quantity * $product->price;

                // Group by category if exists
                $category = $product->category ?? 'Uncategorized';
                if (!isset($analysis['categories'][$category])) {
                    $analysis['categories'][$category] = [
                        'total_products' => 0,
                        'total_value' => 0,
                        'avg_turnover' => 0,
                    ];
                }
                $analysis['categories'][$category]['total_products']++;
                $analysis['categories'][$category]['total_value'] += $product->stock_quantity * $product->price;
            }

            return $analysis;
        });
    }

    public function getReorderRecommendations()
    {
        return Cache::remember('reorder_recommendations', 300, function () {
            return Product::all()
                ->filter(function ($product) {
                    return $this->shouldReorder($product);
                })
                ->map(function ($product) {
                    $salesVelocity = $this->calculateSalesVelocity($product);
                    $recommendedQuantity = $this->calculateReorderQuantity($product, $salesVelocity);

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'current_stock' => $product->stock_quantity,
                        'sales_velocity' => $salesVelocity,
                        'recommended_quantity' => $recommendedQuantity,
                        'estimated_stockout_date' => $salesVelocity > 0 
                            ? now()->addDays($product->stock_quantity / $salesVelocity)
                            : null,
                        'priority' => $this->getReorderPriority($product, $salesVelocity),
                    ];
                })
                ->sortByDesc('priority')
                ->values();
        });
    }

    private function calculateAverageTurnoverRate()
    {
        $products = Product::with(['orderItems' => function ($query) {
            $query->whereHas('order', function ($orderQuery) {
                $orderQuery->where('created_at', '>=', now()->subDays(90));
            });
        }])->get();

        $totalTurnover = 0;
        $validProducts = 0;

        foreach ($products as $product) {
            if ($product->stock_quantity > 0) {
                $totalSold = $product->orderItems->sum('quantity');
                $turnover = ($totalSold / $product->stock_quantity) * (90 / 30);
                $totalTurnover += $turnover;
                $validProducts++;
            }
        }

        return $validProducts > 0 ? round($totalTurnover / $validProducts, 2) : 0;
    }

    private function getFastMovingProductsCount()
    {
        return Product::whereHas('orderItems', function ($query) {
            $query->whereHas('order', function ($orderQuery) {
                $orderQuery->where('created_at', '>=', now()->subDays(30));
            });
        })->count();
    }

    private function getSlowMovingProductsCount()
    {
        return Product::whereDoesntHave('orderItems', function ($query) {
            $query->whereHas('order', function ($orderQuery) {
                $orderQuery->where('created_at', '>=', now()->subDays(60));
            });
        })->count();
    }

    private function calculateInventoryHealthScore()
    {
        $totalProducts = Product::count();
        if ($totalProducts === 0) return 0;

        $activeProducts = $this->getFastMovingProductsCount();
        $slowProducts = $this->getSlowMovingProductsCount();
        $adequateStock = Product::whereBetween('stock_quantity', [10, 100])->count();

        $activeScore = ($activeProducts / $totalProducts) * 40;
        $stockScore = ($adequateStock / $totalProducts) * 35;
        $movementScore = (($totalProducts - $slowProducts) / $totalProducts) * 25;

        return round($activeScore + $stockScore + $movementScore, 1);
    }

    private function categorizeMovement($turnoverRate)
    {
        if ($turnoverRate >= 2) return 'Fast Moving';
        if ($turnoverRate >= 1) return 'Medium Moving';
        if ($turnoverRate >= 0.5) return 'Slow Moving';
        return 'Very Slow';
    }

    private function getReorderStatus($product, $turnoverRate)
    {
        if ($product->stock_quantity <= 0) return 'Out of Stock';
        if ($product->stock_quantity <= 5) return 'Critical';
        if ($product->stock_quantity <= 10 && $turnoverRate >= 1) return 'Low Stock';
        if ($product->stock_quantity >= 100 && $turnoverRate <= 0.5) return 'Overstocked';
        return 'Adequate';
    }

    private function getStockStatus($product)
    {
        if ($product->stock_quantity <= 0) return 'out_of_stock';
        if ($product->stock_quantity <= 10) return 'low_stock';
        if ($product->stock_quantity >= 100) return 'overstocked';
        return 'adequate_stock';
    }

    private function shouldReorder($product)
    {
        $salesVelocity = $this->calculateSalesVelocity($product);
        $daysUntilStockout = $salesVelocity > 0 ? $product->stock_quantity / $salesVelocity : 999;
        
        return $daysUntilStockout <= 14 || $product->stock_quantity <= 5;
    }

    private function calculateSalesVelocity($product)
    {
        $totalSold = $product->orderItems()
            ->whereHas('order', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->sum('quantity');

        return $totalSold / 30; // units per day
    }

    private function calculateReorderQuantity($product, $salesVelocity)
    {
        $leadTimeDays = 7; // Assume 7 days lead time
        $safetyStock = $salesVelocity * 3; // 3 days safety stock
        $demandDuringLeadTime = $salesVelocity * $leadTimeDays;
        
        return max(10, ceil($demandDuringLeadTime + $safetyStock));
    }

    private function getReorderPriority($product, $salesVelocity)
    {
        $daysUntilStockout = $salesVelocity > 0 ? $product->stock_quantity / $salesVelocity : 999;
        
        if ($daysUntilStockout <= 3 || $product->stock_quantity <= 0) return 'Critical';
        if ($daysUntilStockout <= 7) return 'High';
        if ($daysUntilStockout <= 14) return 'Medium';
        return 'Low';
    }
}
