<?php

namespace App\Services;

use App\Models\User;
use App\Models\Application;
use App\Models\RetailerListing;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VendorRetailerService
{
    /**
     * Get retailers connected to a specific vendor through applications
     */
    public function getConnectedRetailers($vendorId, $limit = null)
    {
        $cacheKey = "vendor_{$vendorId}_connected_retailers" . ($limit ? "_{$limit}" : '');

        return Cache::remember($cacheKey, 300, function () use ($vendorId, $limit) {
            $query = User::select('users.id', 'users.name', 'users.email')
                ->join('retailer_listings', 'users.id', '=', 'retailer_listings.retailer_id')
                ->join('applications', 'retailer_listings.application_id', '=', 'applications.id')
                ->where('applications.vendor_id', $vendorId)
                ->where('users.role', 'retailer')
                ->with(['retailerListings' => function ($query) use ($vendorId) {
                    $query->whereHas('application', function ($subQuery) use ($vendorId) {
                        $subQuery->where('vendor_id', $vendorId);
                    })->with('application');
                }])
                ->distinct();

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get()->map(function ($retailer) {
                $listing = $retailer->retailerListings->first();
                return [
                    'id' => $retailer->id,
                    'name' => $retailer->name,
                    'email' => $retailer->email,
                    'application_status' => $listing ? $listing->application->status : 'unknown',
                    'connection_date' => $listing ? $listing->created_at : null,
                ];
            });
        });
    }

    /**
     * Get retailer performance metrics for a specific vendor
     */
    public function getVendorRetailerMetrics($vendorId)
    {
        return Cache::remember("vendor_{$vendorId}_retailer_metrics", 300, function () use ($vendorId) {
            // Get connected retailer IDs
            $connectedRetailerIds = $this->getConnectedRetailerIds($vendorId);

            if ($connectedRetailerIds->isEmpty()) {
                return $this->getEmptyMetrics();
            }

            // Calculate metrics for connected retailers only
            $totalConnected = $connectedRetailerIds->count();

            $activeRetailers = User::whereIn('id', $connectedRetailerIds)
                ->whereHas('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subDays(30));
                })
                ->count();

            $totalOrders = DB::table('orders')
                ->whereIn('user_id', $connectedRetailerIds)
                ->count();

            $totalRevenue = DB::table('orders')
                ->whereIn('user_id', $connectedRetailerIds)
                ->sum('total_amount');

            $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

            return [
                'total_connected_retailers' => $totalConnected,
                'active_retailers' => $activeRetailers,
                'active_percentage' => $totalConnected > 0 ? ($activeRetailers / $totalConnected) * 100 : 0,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'average_order_value' => round($avgOrderValue, 2),
                'orders_per_retailer' => $totalConnected > 0 ? round($totalOrders / $totalConnected, 2) : 0,
            ];
        });
    }

    /**
     * Get top performing retailers for a specific vendor
     */
    public function getTopRetailersForVendor($vendorId, $limit = 5)
    {
        return Cache::remember("vendor_{$vendorId}_top_retailers_{$limit}", 300, function () use ($vendorId, $limit) {
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
     * Get retailer connection summary by application status
     */
    public function getConnectionSummary($vendorId)
    {
        return Cache::remember("vendor_{$vendorId}_connection_summary", 300, function () use ($vendorId) {
            return DB::table('applications')
                ->join('retailer_listings', 'applications.id', '=', 'retailer_listings.application_id')
                ->where('applications.vendor_id', $vendorId)
                ->select('applications.status', DB::raw('COUNT(*) as count'))
                ->groupBy('applications.status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    /**
     * Check if a retailer is connected to a vendor
     */
    public function isRetailerConnected($vendorId, $retailerId)
    {
        return RetailerListing::where('retailer_id', $retailerId)
            ->whereHas('application', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->exists();
    }

    /**
     * Get connected retailer IDs for a vendor
     */
    private function getConnectedRetailerIds($vendorId)
    {
        return RetailerListing::whereHas('application', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->pluck('retailer_id');
    }

    /**
     * Get empty metrics structure
     */
    private function getEmptyMetrics()
    {
        return [
            'total_connected_retailers' => 0,
            'active_retailers' => 0,
            'active_percentage' => 0,
            'total_orders' => 0,
            'total_revenue' => 0,
            'average_order_value' => 0,
            'orders_per_retailer' => 0,
        ];
    }

    /**
     * Clear cache for vendor retailer data
     */
    public function clearVendorCache($vendorId)
    {
        $patterns = [
            "vendor_{$vendorId}_connected_retailers*",
            "vendor_{$vendorId}_retailer_metrics",
            "vendor_{$vendorId}_top_retailers*",
            "vendor_{$vendorId}_connection_summary"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}
