<?php

namespace App\Services;

use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class RetailerInventoryService
{
    public function getInventoryRecommendations()
    {
        return Cache::remember('retailer_inventory_recommendations', 300, function () {
            $recommendations = [];
            
            // Get frequently purchased products
            $frequentProducts = $this->getFrequentlyPurchasedProducts();
            
            // Get seasonal recommendations
            $seasonalProducts = $this->getSeasonalRecommendations();
            
            // Get trending products
            $trendingProducts = $this->getTrendingProducts();
            
            // Get low stock alerts for products previously ordered
            $lowStockAlerts = $this->getLowStockAlerts();
            
            return [
                'frequent_replenishment' => $frequentProducts,
                'seasonal_opportunities' => $seasonalProducts,
                'trending_products' => $trendingProducts,
                'low_stock_alerts' => $lowStockAlerts,
                'personalized_suggestions' => $this->getPersonalizedSuggestions(),
            ];
        });
    }

    public function getPurchaseAnalytics()
    {
        return Cache::remember('retailer_purchase_analytics', 300, function () {
            $totalProducts = $this->getTotalProductsPurchased();
            $avgOrderSize = $this->getAverageOrderSize();
            $topCategories = $this->getTopCategoriesPurchased();
            $purchaseVelocity = $this->getPurchaseVelocity();
            
            return [
                'total_unique_products' => $totalProducts,
                'average_order_size' => $avgOrderSize,
                'top_categories' => $topCategories,
                'purchase_velocity' => $purchaseVelocity,
                'inventory_diversity_score' => $this->calculateInventoryDiversityScore(),
                'reorder_predictions' => $this->getReorderPredictions(),
            ];
        });
    }

    public function getStockOptimizationSuggestions()
    {
        return Cache::remember('retailer_stock_optimization', 300, function () {
            $suggestions = [];
            
            // Analyze purchase patterns
            $purchaseHistory = $this->analyzePurchaseHistory();
            
            foreach ($purchaseHistory as $product) {
                $suggestion = $this->generateStockSuggestion($product);
                if ($suggestion) {
                    $suggestions[] = $suggestion;
                }
            }
            
            return collect($suggestions)->sortByDesc('priority_score')->take(15)->values();
        });
    }

    public function getInventoryPerformanceMetrics()
    {
        return Cache::remember('retailer_inventory_performance', 300, function () {
            $ordersLast30Days = Order::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            
            $avgItemsPerOrder = OrderItem::whereHas('order', function ($query) {
                $query->where('user_id', auth()->id())
                      ->where('created_at', '>=', now()->subDays(30));
            })->avg('quantity') ?? 0;
            
            $orderConsistency = $this->calculateOrderConsistency();
            $categoryDiversification = $this->calculateCategoryDiversification();
            
            return [
                'orders_frequency' => $ordersLast30Days,
                'avg_items_per_order' => round($avgItemsPerOrder, 1),
                'order_consistency_score' => $orderConsistency,
                'category_diversification' => $categoryDiversification,
                'inventory_turnover_estimate' => $this->estimateInventoryTurnover(),
                'optimal_order_frequency' => $this->calculateOptimalOrderFrequency(),
            ];
        });
    }

    private function getFrequentlyPurchasedProducts()
    {
        return OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id())
                  ->where('created_at', '>=', now()->subDays(90));
        })
            ->with('product')
            ->select('product_id', DB::raw('COUNT(*) as order_frequency'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->having('order_frequency', '>=', 2)
            ->orderByDesc('order_frequency')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $lastOrderDate = OrderItem::whereHas('order', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                    ->where('product_id', $item->product_id)
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->max('orders.created_at');
                
                $daysSinceLastOrder = $lastOrderDate ? now()->diffInDays($lastOrderDate) : 999;
                
                return [
                    'product' => $item->product,
                    'order_frequency' => $item->order_frequency,
                    'total_quantity' => $item->total_quantity,
                    'days_since_last_order' => $daysSinceLastOrder,
                    'recommendation_score' => $this->calculateRecommendationScore($item->order_frequency, $daysSinceLastOrder),
                    'suggested_quantity' => max(1, round($item->total_quantity / $item->order_frequency)),
                ];
            });
    }

    private function getSeasonalRecommendations()
    {
        $currentMonth = now()->month;
        $currentQuarter = now()->quarter;
        
        // Get products ordered in the same period last year
        return OrderItem::whereHas('order', function ($query) use ($currentMonth) {
            $query->where('user_id', auth()->id())
                  ->whereMonth('created_at', $currentMonth)
                  ->whereYear('created_at', '>=', now()->subYear()->year);
        })
            ->with('product')
            ->select('product_id', DB::raw('SUM(quantity) as seasonal_quantity'), DB::raw('COUNT(DISTINCT YEAR(orders.created_at)) as years_ordered'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('product_id')
            ->having('years_ordered', '>=', 1)
            ->orderByDesc('seasonal_quantity')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                return [
                    'product' => $item->product,
                    'seasonal_quantity' => $item->seasonal_quantity,
                    'years_ordered' => $item->years_ordered,
                    'confidence_score' => min(100, ($item->years_ordered / 3) * 100),
                    'recommended_stock' => round($item->seasonal_quantity / $item->years_ordered),
                ];
            });
    }

    private function getTrendingProducts()
    {
        // Simple trending algorithm based on recent increase in orders across all users
        return Product::whereHas('orderItems', function ($query) {
            $query->whereHas('order', function ($orderQuery) {
                $orderQuery->where('created_at', '>=', now()->subDays(30));
            });
        })
            ->withCount(['orderItems as recent_orders' => function ($query) {
                $query->whereHas('order', function ($orderQuery) {
                    $orderQuery->where('created_at', '>=', now()->subDays(30));
                });
            }])
            ->withCount(['orderItems as previous_orders' => function ($query) {
                $query->whereHas('order', function ($orderQuery) {
                    $orderQuery->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)]);
                });
            }])
            ->having('recent_orders', '>', 'previous_orders')
            ->orderByRaw('recent_orders - previous_orders DESC')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                $trendScore = $product->recent_orders - $product->previous_orders;
                return [
                    'product' => $product,
                    'trend_score' => $trendScore,
                    'recent_orders' => $product->recent_orders,
                    'growth_rate' => $product->previous_orders > 0 
                        ? round((($product->recent_orders - $product->previous_orders) / $product->previous_orders) * 100, 1)
                        : 100,
                ];
            });
    }

    private function getLowStockAlerts()
    {
        // Get products that are frequently ordered but haven't been ordered recently
        return OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with('product')
            ->select('product_id', DB::raw('MAX(orders.created_at) as last_order_date'), DB::raw('COUNT(*) as total_orders'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('product_id')
            ->having('total_orders', '>=', 3)
            ->having('last_order_date', '<=', now()->subDays(45))
            ->orderBy('last_order_date')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                $daysSinceLastOrder = now()->diffInDays($item->last_order_date);
                return [
                    'product' => $item->product,
                    'last_order_date' => $item->last_order_date,
                    'days_since_last_order' => $daysSinceLastOrder,
                    'total_orders' => $item->total_orders,
                    'urgency_level' => $this->calculateUrgencyLevel($daysSinceLastOrder, $item->total_orders),
                ];
            });
    }

    private function getPersonalizedSuggestions()
    {
        // Get products ordered by similar retailers
        $myCategories = $this->getMyTopCategories();
        
        return Product::whereHas('orderItems', function ($query) use ($myCategories) {
            $query->whereHas('order', function ($orderQuery) {
                $orderQuery->where('user_id', '!=', auth()->id())
                          ->whereHas('user', function ($userQuery) {
                              $userQuery->where('role', 'retailer');
                          });
            });
        })
            ->where(function ($query) use ($myCategories) {
                foreach ($myCategories as $category) {
                    $query->orWhere('category', 'like', "%{$category}%");
                }
            })
            ->whereNotIn('id', function ($query) {
                $query->select('product_id')
                      ->from('order_items')
                      ->join('orders', 'order_items.order_id', '=', 'orders.id')
                      ->where('orders.user_id', auth()->id());
            })
            ->withCount(['orderItems as popularity_score' => function ($query) {
                $query->whereHas('order', function ($orderQuery) {
                    $orderQuery->where('created_at', '>=', now()->subDays(90))
                              ->whereHas('user', function ($userQuery) {
                                  $userQuery->where('role', 'retailer');
                              });
                });
            }])
            ->orderByDesc('popularity_score')
            ->limit(6)
            ->get()
            ->map(function ($product) {
                return [
                    'product' => $product,
                    'popularity_score' => $product->popularity_score,
                    'suggestion_reason' => 'Popular among similar retailers',
                ];
            });
    }

    private function calculateRecommendationScore($frequency, $daysSinceLastOrder)
    {
        $frequencyScore = min(100, $frequency * 20);
        $recencyScore = max(0, 100 - ($daysSinceLastOrder * 2));
        return ($frequencyScore + $recencyScore) / 2;
    }

    private function calculateUrgencyLevel($days, $totalOrders)
    {
        $baseUrgency = min(100, $days * 2);
        $frequencyMultiplier = min(2, $totalOrders / 5);
        $urgencyScore = $baseUrgency * $frequencyMultiplier;
        
        if ($urgencyScore >= 80) return 'High';
        if ($urgencyScore >= 50) return 'Medium';
        return 'Low';
    }

    private function getTotalProductsPurchased()
    {
        return OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })->distinct('product_id')->count();
    }

    private function getAverageOrderSize()
    {
        return OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })->avg('quantity') ?? 0;
    }

    private function getTopCategoriesPurchased()
    {
        return Product::whereHas('orderItems', function ($query) {
            $query->whereHas('order', function ($orderQuery) {
                $orderQuery->where('user_id', auth()->id());
            });
        })
            ->select('category', DB::raw('COUNT(*) as purchase_count'))
            ->groupBy('category')
            ->orderByDesc('purchase_count')
            ->limit(5)
            ->pluck('purchase_count', 'category')
            ->toArray();
    }

    private function getPurchaseVelocity()
    {
        $orders = Order::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(90))
            ->count();
        
        return round($orders / 90, 2); // Orders per day
    }

    private function calculateInventoryDiversityScore()
    {
        $categories = $this->getTopCategoriesPurchased();
        $totalCategories = count($categories);
        $maxPossibleCategories = Product::distinct('category')->count();
        
        return $maxPossibleCategories > 0 ? round(($totalCategories / $maxPossibleCategories) * 100, 1) : 0;
    }

    private function getMyTopCategories()
    {
        return array_keys($this->getTopCategoriesPurchased());
    }

    private function analyzePurchaseHistory()
    {
        return OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id())
                  ->where('created_at', '>=', now()->subDays(180));
        })
            ->with('product')
            ->select('product_id', DB::raw('COUNT(*) as frequency'), DB::raw('AVG(quantity) as avg_quantity'))
            ->groupBy('product_id')
            ->get();
    }

    private function generateStockSuggestion($product)
    {
        if ($product->frequency < 2) return null;
        
        return [
            'product' => $product->product,
            'current_frequency' => $product->frequency,
            'suggested_quantity' => max(1, round($product->avg_quantity * 1.2)),
            'priority_score' => $product->frequency * 10,
            'reason' => 'Based on purchase history',
        ];
    }

    private function calculateOrderConsistency()
    {
        $orders = Order::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(90))
            ->orderBy('created_at')
            ->pluck('created_at')
            ->toArray();
        
        if (count($orders) < 2) return 0;
        
        $intervals = [];
        for ($i = 1; $i < count($orders); $i++) {
            $diff = strtotime($orders[$i]) - strtotime($orders[$i - 1]);
            $intervals[] = $diff / (24 * 60 * 60); // Convert to days
        }
        
        $avgInterval = array_sum($intervals) / count($intervals);
        $variance = array_sum(array_map(function ($x) use ($avgInterval) {
            return pow($x - $avgInterval, 2);
        }, $intervals)) / count($intervals);
        
        $coefficient = $avgInterval > 0 ? sqrt($variance) / $avgInterval : 0;
        
        return max(0, round((1 - min(1, $coefficient)) * 100, 1));
    }

    private function calculateCategoryDiversification()
    {
        $categories = $this->getTopCategoriesPurchased();
        return count($categories);
    }

    private function estimateInventoryTurnover()
    {
        $velocity = $this->getPurchaseVelocity();
        return $velocity * 30; // Monthly turnover estimate
    }

    private function calculateOptimalOrderFrequency()
    {
        $velocity = $this->getPurchaseVelocity();
        if ($velocity >= 1) return 'Weekly';
        if ($velocity >= 0.5) return 'Bi-weekly';
        if ($velocity >= 0.25) return 'Monthly';
        return 'Quarterly';
    }

    private function getReorderPredictions()
    {
        return $this->getFrequentlyPurchasedProducts()
            ->filter(function ($item) {
                return $item['days_since_last_order'] >= 30;
            })
            ->map(function ($item) {
                return [
                    'product_name' => $item['product']->name,
                    'predicted_reorder_date' => now()->addDays(7),
                    'confidence' => min(100, $item['recommendation_score']),
                ];
            })
            ->take(5);
    }
}
