<?php

namespace App\Services;

use App\Models\Product;
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
            $ordersLast30Days = Order::where('buyer_id', auth()->id())
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            
            // Calculate average items per order from JSON items
            $orders = Order::where('buyer_id', auth()->id())
                ->where('created_at', '>=', now()->subDays(30))
                ->get();
            
            $totalItems = 0;
            $totalOrders = $orders->count();
            
            foreach ($orders as $order) {
                $items = $order->getItemsAsArray();
                foreach ($items as $item) {
                    $totalItems += $item['quantity'] ?? 0;
                }
            }
            
            $avgItemsPerOrder = $totalOrders > 0 ? $totalItems / $totalOrders : 0;
            
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
        // Simplified implementation using Order's JSON items
        $orders = Order::where('buyer_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(90))
            ->get();
        
        $productStats = [];
        
        foreach ($orders as $order) {
            $items = $order->getItemsAsArray();
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? 0;
                
                if ($productId) {
                    if (!isset($productStats[$productId])) {
                        $productStats[$productId] = [
                            'product_id' => $productId,
                            'order_frequency' => 0,
                            'total_quantity' => 0,
                            'last_order_date' => null,
                        ];
                    }
                    
                    $productStats[$productId]['order_frequency']++;
                    $productStats[$productId]['total_quantity'] += $quantity;
                    $productStats[$productId]['last_order_date'] = max(
                        $productStats[$productId]['last_order_date'], 
                        $order->created_at
                    );
                }
            }
        }
        
        // Filter products with frequency >= 2 and return top 10
        return collect($productStats)
            ->filter(function ($stats) {
                return $stats['order_frequency'] >= 2;
            })
            ->sortByDesc('order_frequency')
            ->take(10)
            ->map(function ($stats) {
                $product = Product::find($stats['product_id']);
                $daysSinceLastOrder = $stats['last_order_date'] 
                    ? now()->diffInDays($stats['last_order_date']) 
                    : 999;
                
                return [
                    'product' => $product,
                    'order_frequency' => $stats['order_frequency'],
                    'total_quantity' => $stats['total_quantity'],
                    'days_since_last_order' => $daysSinceLastOrder,
                    'recommendation_score' => $this->calculateRecommendationScore($stats['order_frequency'], $daysSinceLastOrder),
                    'suggested_quantity' => max(1, round($stats['total_quantity'] / $stats['order_frequency'])),
                ];
            });
    }

    private function getSeasonalRecommendations()
    {
        $currentMonth = now()->month;
        
        // Get products ordered in the same period last year using JSON items
        $orders = Order::where('buyer_id', auth()->id())
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', '>=', now()->subYear()->year)
            ->get();
        
        $productStats = [];
        
        foreach ($orders as $order) {
            $items = $order->getItemsAsArray();
            $year = $order->created_at->year;
            
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? 0;
                
                if ($productId) {
                    if (!isset($productStats[$productId])) {
                        $productStats[$productId] = [
                            'product_id' => $productId,
                            'seasonal_quantity' => 0,
                            'years_ordered' => [],
                        ];
                    }
                    
                    $productStats[$productId]['seasonal_quantity'] += $quantity;
                    $productStats[$productId]['years_ordered'][$year] = true;
                }
            }
        }
        
        return collect($productStats)
            ->filter(function ($stats) {
                return count($stats['years_ordered']) >= 1;
            })
            ->sortByDesc('seasonal_quantity')
            ->take(8)
            ->map(function ($stats) {
                $product = Product::find($stats['product_id']);
                $yearsOrdered = count($stats['years_ordered']);
                
                return [
                    'product' => $product,
                    'seasonal_quantity' => $stats['seasonal_quantity'],
                    'years_ordered' => $yearsOrdered,
                    'confidence_score' => min(100, ($yearsOrdered / 3) * 100),
                    'recommended_stock' => round($stats['seasonal_quantity'] / $yearsOrdered),
                ];
            });
    }

    private function getTrendingProducts()
    {
        // Simplified trending algorithm - products with increasing orders across all users
        $recentOrders = Order::where('created_at', '>=', now()->subDays(30))->get();
        $previousOrders = Order::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->get();
        
        $recentProductStats = [];
        $previousProductStats = [];
        
        // Count recent orders for each product
        foreach ($recentOrders as $order) {
            $items = $order->getItemsAsArray();
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                if ($productId) {
                    $recentProductStats[$productId] = ($recentProductStats[$productId] ?? 0) + 1;
                }
            }
        }
        
        // Count previous orders for each product
        foreach ($previousOrders as $order) {
            $items = $order->getItemsAsArray();
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                if ($productId) {
                    $previousProductStats[$productId] = ($previousProductStats[$productId] ?? 0) + 1;
                }
            }
        }
        
        // Calculate trending products
        $trendingProducts = [];
        foreach ($recentProductStats as $productId => $recentCount) {
            $previousCount = $previousProductStats[$productId] ?? 0;
            $trendScore = $recentCount - $previousCount;
            
            if ($trendScore > 0) {
                $product = Product::find($productId);
                if ($product) {
                    $trendingProducts[] = [
                        'product' => $product,
                        'trend_score' => $trendScore,
                        'recent_orders' => $recentCount,
                        'growth_rate' => $previousCount > 0 
                            ? round((($recentCount - $previousCount) / $previousCount) * 100, 1)
                            : 100,
                    ];
                }
            }
        }
        
        return collect($trendingProducts)
            ->sortByDesc('trend_score')
            ->take(5)
            ->values();
    }

    private function getLowStockAlerts()
    {
        // Get products that are frequently ordered but haven't been ordered recently
        $orders = Order::where('buyer_id', auth()->id())->get();
        
        $productStats = [];
        
        foreach ($orders as $order) {
            $items = $order->getItemsAsArray();
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                if ($productId) {
                    if (!isset($productStats[$productId])) {
                        $productStats[$productId] = [
                            'product_id' => $productId,
                            'total_orders' => 0,
                            'last_order_date' => null,
                        ];
                    }
                    
                    $productStats[$productId]['total_orders']++;
                    $productStats[$productId]['last_order_date'] = max(
                        $productStats[$productId]['last_order_date'],
                        $order->created_at
                    );
                }
            }
        }
        
        return collect($productStats)
            ->filter(function ($stats) {
                return $stats['total_orders'] >= 3 && 
                       $stats['last_order_date'] && 
                       now()->diffInDays($stats['last_order_date']) >= 45;
            })
            ->sortBy('last_order_date')
            ->take(8)
            ->map(function ($stats) {
                $product = Product::find($stats['product_id']);
                $daysSinceLastOrder = now()->diffInDays($stats['last_order_date']);
                
                return [
                    'product' => $product,
                    'last_order_date' => $stats['last_order_date'],
                    'days_since_last_order' => $daysSinceLastOrder,
                    'total_orders' => $stats['total_orders'],
                    'urgency_level' => $this->calculateUrgencyLevel($daysSinceLastOrder, $stats['total_orders']),
                ];
            })
            ->values();
    }

    private function getPersonalizedSuggestions()
    {
        // Simplified: Get products from top categories that haven't been ordered recently
        $myCategories = $this->getMyTopCategories();
        
        if (empty($myCategories)) {
            return collect([]);
        }
        
        // Get products I've ordered to exclude them
        $myProductIds = [];
        $myOrders = Order::where('buyer_id', auth()->id())->get();
        
        foreach ($myOrders as $order) {
            $items = $order->getItemsAsArray();
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                if ($productId) {
                    $myProductIds[] = $productId;
                }
            }
        }
        
        $myProductIds = array_unique($myProductIds);
        
        // Get products from my top categories that I haven't ordered
        $suggestions = Product::where(function ($query) use ($myCategories) {
                foreach ($myCategories as $category) {
                    $query->orWhere('category', 'like', "%{$category}%");
                }
            })
            ->whereNotIn('id', $myProductIds)
            ->inRandomOrder()
            ->limit(6)
            ->get()
            ->map(function ($product) {
                return [
                    'product' => $product,
                    'suggestion_reason' => 'Popular in your category',
                    'confidence_score' => rand(70, 95),
                ];
            });
        
        return $suggestions;
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
