<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RetailerAnalyticsService
{
    public function getTopRetailers($limit = 10)
    {
        return Cache::remember('vendor_top_retailers', 300, function () use ($limit) {
            return User::where('role', 'retailer')
                ->withCount(['orders as total_orders'])
                ->withSum('orders as total_revenue', 'total_amount')
                ->orderByDesc('total_revenue')
                ->limit($limit)
                ->get()
                ->map(function ($retailer) {
                    return [
                        'id' => $retailer->id,
                        'name' => $retailer->name,
                        'email' => $retailer->email,
                        'total_orders' => $retailer->total_orders ?? 0,
                        'total_revenue' => $retailer->total_revenue ?? 0,
                        'average_order_value' => $retailer->total_orders > 0
                            ? ($retailer->total_revenue / $retailer->total_orders)
                            : 0,
                    ];
                });
        });
    }

    public function getRetailerPerformanceMetrics()
    {
        return Cache::remember('retailer_performance_metrics', 300, function () {
            $totalRetailers = User::where('role', 'retailer')->count();
            $activeRetailers = User::where('role', 'retailer')
                ->whereHas('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subDays(30));
                })
                ->count();

            $avgOrdersPerRetailer = $totalRetailers > 0
                ? Order::whereHas('user', function ($query) {
                    $query->where('role', 'retailer');
                })->count() / $totalRetailers
                : 0;

            return [
                'total_retailers' => $totalRetailers,
                'active_retailers' => $activeRetailers,
                'active_percentage' => $totalRetailers > 0 ? ($activeRetailers / $totalRetailers) * 100 : 0,
                'average_orders_per_retailer' => round($avgOrdersPerRetailer, 2),
                'retailer_retention_rate' => $this->calculateRetailerRetentionRate(),
            ];
        });
    }

    public function getRetailerOrderFrequency()
    {
        return Cache::remember('retailer_order_frequency', 300, function () {
            return DB::table('orders')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('users.role', 'retailer')
                ->select(
                    'users.name as retailer_name',
                    'users.id as retailer_id',
                    DB::raw('COUNT(orders.id) as order_count'),
                    DB::raw('AVG(DATEDIFF(NOW(), orders.created_at)) as avg_days_since_last_order'),
                    DB::raw('MAX(orders.created_at) as last_order_date')
                )
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('order_count')
                ->get()
                ->map(function ($item) {
                    return [
                        'retailer_id' => $item->retailer_id,
                        'retailer_name' => $item->retailer_name,
                        'order_count' => $item->order_count,
                        'avg_days_since_last_order' => round($item->avg_days_since_last_order, 1),
                        'last_order_date' => $item->last_order_date,
                        'frequency_category' => $this->categorizeOrderFrequency($item->avg_days_since_last_order),
                    ];
                });
        });
    }

    public function getRetailerGrowthTrends($months = 6)
    {
        return Cache::remember("retailer_growth_trends_{$months}m", 300, function () use ($months) {
            $data = [];
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthStart = $date->startOfMonth()->copy();
                $monthEnd = $date->endOfMonth()->copy();

                $newRetailers = User::where('role', 'retailer')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();

                $activeRetailers = User::where('role', 'retailer')
                    ->whereHas('orders', function ($query) use ($monthStart, $monthEnd) {
                        $query->whereBetween('created_at', [$monthStart, $monthEnd]);
                    })
                    ->count();

                $data[] = [
                    'month' => $date->format('M Y'),
                    'new_retailers' => $newRetailers,
                    'active_retailers' => $activeRetailers,
                    'total_orders' => Order::whereHas('user', function ($query) {
                        $query->where('role', 'retailer');
                    })->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                ];
            }

            return $data;
        });
    }

    private function calculateRetailerRetentionRate()
    {
        $retailersLastMonth = User::where('role', 'retailer')
            ->whereHas('orders', function ($query) {
                $query->whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ]);
            })
            ->count();

        $retailersThisMonth = User::where('role', 'retailer')
            ->whereHas('orders', function ($query) {
                $query->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ]);
            })
            ->count();

        return $retailersLastMonth > 0 ? ($retailersThisMonth / $retailersLastMonth) * 100 : 0;
    }

    private function categorizeOrderFrequency($avgDays)
    {
        if ($avgDays <= 7) return 'Very High';
        if ($avgDays <= 14) return 'High';
        if ($avgDays <= 30) return 'Medium';
        if ($avgDays <= 60) return 'Low';
        return 'Very Low';
    }

    /**
     * Get top retailers for a specific vendor (vendor-specific context)
     */
    public function getTopRetailersForVendor($vendorId, $limit = 10)
    {
        return Cache::remember("vendor_{$vendorId}_top_retailers_analytics", 300, function () use ($vendorId, $limit) {
            $connectedRetailerIds = $this->getConnectedRetailerIds($vendorId);

            if ($connectedRetailerIds->isEmpty()) {
                return collect();
            }

            return User::whereIn('id', $connectedRetailerIds)
                ->withCount(['orders as total_orders'])
                ->withSum('orders as total_revenue', 'total_amount')
                ->orderByDesc('total_revenue')
                ->limit($limit)
                ->get()
                ->map(function ($retailer) {
                    return [
                        'id' => $retailer->id,
                        'name' => $retailer->name,
                        'email' => $retailer->email,
                        'total_orders' => $retailer->total_orders ?? 0,
                        'total_revenue' => $retailer->total_revenue ?? 0,
                        'average_order_value' => $retailer->total_orders > 0
                            ? ($retailer->total_revenue / $retailer->total_orders)
                            : 0,
                    ];
                });
        });
    }

    /**
     * Get retailer performance metrics for a specific vendor
     */
    public function getRetailerPerformanceMetricsForVendor($vendorId)
    {
        return Cache::remember("vendor_{$vendorId}_retailer_performance", 300, function () use ($vendorId) {
            $connectedRetailerIds = $this->getConnectedRetailerIds($vendorId);

            if ($connectedRetailerIds->isEmpty()) {
                return $this->getEmptyVendorMetrics();
            }

            $totalRetailers = $connectedRetailerIds->count();
            $activeRetailers = User::whereIn('id', $connectedRetailerIds)
                ->whereHas('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subDays(30));
                })
                ->count();

            $avgOrdersPerRetailer = $totalRetailers > 0
                ? Order::whereIn('user_id', $connectedRetailerIds)->count() / $totalRetailers
                : 0;

            return [
                'total_retailers' => $totalRetailers,
                'active_retailers' => $activeRetailers,
                'active_percentage' => $totalRetailers > 0 ? ($activeRetailers / $totalRetailers) * 100 : 0,
                'average_orders_per_retailer' => round($avgOrdersPerRetailer, 2),
                'retailer_retention_rate' => $this->calculateRetailerRetentionRateForVendor($vendorId),
            ];
        });
    }

    /**
     * Get retailer order frequency for a specific vendor
     */
    public function getRetailerOrderFrequencyForVendor($vendorId)
    {
        return Cache::remember("vendor_{$vendorId}_retailer_frequency", 300, function () use ($vendorId) {
            $connectedRetailerIds = $this->getConnectedRetailerIds($vendorId);

            if ($connectedRetailerIds->isEmpty()) {
                return collect();
            }

            return DB::table('orders')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->whereIn('users.id', $connectedRetailerIds)
                ->where('users.role', 'retailer')
                ->select(
                    'users.name as retailer_name',
                    'users.id as retailer_id',
                    DB::raw('COUNT(orders.id) as order_count'),
                    DB::raw('AVG(DATEDIFF(NOW(), orders.created_at)) as avg_days_since_last_order'),
                    DB::raw('MAX(orders.created_at) as last_order_date')
                )
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('order_count')
                ->get()
                ->map(function ($item) {
                    return [
                        'retailer_id' => $item->retailer_id,
                        'retailer_name' => $item->retailer_name,
                        'order_count' => $item->order_count,
                        'avg_days_since_last_order' => round($item->avg_days_since_last_order, 1),
                        'last_order_date' => $item->last_order_date,
                        'frequency_category' => $this->categorizeOrderFrequency($item->avg_days_since_last_order),
                    ];
                });
        });
    }

    /**
     * Get connected retailer IDs for a vendor
     */
    private function getConnectedRetailerIds($vendorId)
    {
        return \App\Models\RetailerListing::whereHas('application', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->pluck('retailer_id');
    }

    /**
     * Calculate retailer retention rate for a specific vendor
     */
    private function calculateRetailerRetentionRateForVendor($vendorId)
    {
        $connectedRetailerIds = $this->getConnectedRetailerIds($vendorId);

        if ($connectedRetailerIds->isEmpty()) {
            return 0;
        }

        $retailersLastMonth = User::whereIn('id', $connectedRetailerIds)
            ->whereHas('orders', function ($query) {
                $query->whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ]);
            })
            ->count();

        $retailersThisMonth = User::whereIn('id', $connectedRetailerIds)
            ->whereHas('orders', function ($query) {
                $query->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ]);
            })
            ->count();

        return $retailersLastMonth > 0 ? ($retailersThisMonth / $retailersLastMonth) * 100 : 0;
    }

    /**
     * Get empty metrics structure for vendor context
     */
    private function getEmptyVendorMetrics()
    {
        return [
            'total_retailers' => 0,
            'active_retailers' => 0,
            'active_percentage' => 0,
            'average_orders_per_retailer' => 0,
            'retailer_retention_rate' => 0,
        ];
    }
}
