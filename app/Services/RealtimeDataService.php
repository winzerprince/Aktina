<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Resource;
use App\Models\Inventory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RealtimeDataService
{
    protected $cacheTimeout = 30; // 30 seconds for real-time data

    /**
     * Get real-time dashboard metrics for all roles
     */
    public function getRealtimeDashboardMetrics(): array
    {
        return Cache::remember('realtime_dashboard_metrics', $this->cacheTimeout, function () {
            return [
                'total_users' => User::count(),
                'active_orders' => Order::whereIn('status', ['pending', 'processing'])->count(),
                'total_products' => Product::count(),
                'total_resources' => Resource::count(),
                'low_stock_items' => $this->getLowStockCount(),
                'recent_activities' => $this->getRecentActivities(),
                'system_status' => $this->getSystemStatus(),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get real-time inventory updates
     */
    public function getRealtimeInventoryData(): array
    {
        return Cache::remember('realtime_inventory', $this->cacheTimeout, function () {
            $products = Product::with('inventory')->get();
            $resources = Resource::with('inventory')->get();

            return [
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'current_stock' => $product->inventory->quantity ?? 0,
                        'reserved_stock' => 0, // Could be enhanced with reserved quantity
                        'available_stock' => $product->inventory->quantity ?? 0,
                        'status' => $this->getStockStatus($product->inventory->quantity ?? 0),
                        'last_updated' => $product->updated_at->toISOString(),
                    ];
                }),
                'resources' => $resources->map(function ($resource) {
                    return [
                        'id' => $resource->id,
                        'name' => $resource->name,
                        'current_stock' => $resource->inventory->quantity ?? 0,
                        'unit' => $resource->unit,
                        'status' => $this->getStockStatus($resource->inventory->quantity ?? 0),
                        'last_updated' => $resource->updated_at->toISOString(),
                    ];
                }),
                'alerts' => $this->getInventoryAlerts(),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get real-time order updates
     */
    public function getRealtimeOrderData(): array
    {
        return Cache::remember('realtime_orders', $this->cacheTimeout, function () {
            $recentOrders = Order::with(['user'])
                ->latest()
                ->limit(10)
                ->get();

            return [
                'recent_orders' => $recentOrders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'user_name' => $order->user->name,
                        'status' => $order->status,
                        'total_amount' => $order->total_amount,
                        'created_at' => $order->created_at->toISOString(),
                    ];
                }),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'processing_orders' => Order::where('status', 'processing')->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get real-time production metrics
     */
    public function getRealtimeProductionData(): array
    {
        return Cache::remember('realtime_production', $this->cacheTimeout, function () {
            $totalOrders = Order::count();
            $completedOrders = Order::where('status', 'completed')->count();
            $processingOrders = Order::where('status', 'processing')->count();

            $efficiency = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
            $fulfillmentRate = $totalOrders > 0 ? (($completedOrders + $processingOrders) / $totalOrders) * 100 : 0;

            return [
                'efficiency_rate' => round($efficiency, 2),
                'fulfillment_rate' => round($fulfillmentRate, 2),
                'active_productions' => $processingOrders,
                'completed_today' => Order::where('status', 'completed')
                    ->whereDate('updated_at', today())
                    ->count(),
                'resource_utilization' => $this->getResourceUtilization(),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get real-time notifications
     */
    public function getRealtimeNotifications(int $userId): array
    {
        return Cache::remember("realtime_notifications_{$userId}", $this->cacheTimeout, function () use ($userId) {
            return [
                'inventory_alerts' => $this->getInventoryAlerts(),
                'order_notifications' => $this->getOrderNotifications($userId),
                'system_alerts' => $this->getSystemAlerts(),
                'unread_count' => $this->getUnreadNotificationCount($userId),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Clear cache for specific data type
     */
    public function clearRealtimeCache(string $type = null): bool
    {
        $cacheKeys = [
            'realtime_dashboard_metrics',
            'realtime_inventory',
            'realtime_orders',
            'realtime_production',
        ];

        if ($type) {
            return Cache::forget("realtime_{$type}");
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        return true;
    }

    /**
     * Get low stock count
     */
    private function getLowStockCount(): int
    {
        $lowStockProducts = Product::whereHas('inventory', function ($query) {
            $query->where('quantity', '<', 10); // Threshold of 10
        })->count();

        $lowStockResources = Resource::whereHas('inventory', function ($query) {
            $query->where('quantity', '<', 5); // Threshold of 5
        })->count();

        return $lowStockProducts + $lowStockResources;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(): array
    {
        $recentOrders = Order::latest()->limit(3)->get();
        $recentUsers = User::latest()->limit(2)->get();

        $activities = [];

        foreach ($recentOrders as $order) {
            $activities[] = [
                'type' => 'order',
                'message' => "New order #{$order->id} created",
                'time' => $order->created_at->diffForHumans(),
            ];
        }

        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user',
                'message' => "New user {$user->name} registered",
                'time' => $user->created_at->diffForHumans(),
            ];
        }

        return collect($activities)->sortByDesc('time')->take(5)->values()->all();
    }

    /**
     * Get system status
     */
    private function getSystemStatus(): array
    {
        return [
            'status' => 'healthy',
            'uptime' => '99.9%',
            'response_time' => '120ms',
            'active_users' => User::where('last_login_at', '>', now()->subHours(24))->count(),
        ];
    }

    /**
     * Get stock status
     */
    private function getStockStatus(int $quantity): string
    {
        if ($quantity === 0) return 'out_of_stock';
        if ($quantity < 10) return 'low_stock';
        if ($quantity < 50) return 'medium_stock';
        return 'healthy_stock';
    }

    /**
     * Get inventory alerts
     */
    private function getInventoryAlerts(): array
    {
        $alerts = [];

        // Check for low stock products
        $lowStockProducts = Product::whereHas('inventory', function ($query) {
            $query->where('quantity', '<', 10);
        })->with('inventory')->get();

        foreach ($lowStockProducts as $product) {
            $alerts[] = [
                'type' => 'low_stock',
                'item' => $product->name,
                'current_stock' => $product->inventory->quantity,
                'severity' => $product->inventory->quantity === 0 ? 'critical' : 'warning',
                'created_at' => now()->toISOString(),
            ];
        }

        return $alerts;
    }

    /**
     * Get resource utilization
     */
    private function getResourceUtilization(): array
    {
        $totalResources = Resource::count();
        $usedResources = Resource::whereHas('inventory', function ($query) {
            $query->where('quantity', '>', 0);
        })->count();

        return [
            'total' => $totalResources,
            'used' => $usedResources,
            'utilization_rate' => $totalResources > 0 ? ($usedResources / $totalResources) * 100 : 0,
        ];
    }

    /**
     * Get order notifications
     */
    private function getOrderNotifications(int $userId): array
    {
        // This would be enhanced with a proper notifications table
        return [
            [
                'type' => 'order_status',
                'message' => 'Your order has been processed',
                'created_at' => now()->subMinutes(5)->toISOString(),
            ]
        ];
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts(): array
    {
        return [
            [
                'type' => 'system',
                'message' => 'System maintenance scheduled for tonight',
                'severity' => 'info',
                'created_at' => now()->subHours(1)->toISOString(),
            ]
        ];
    }

    /**
     * Get unread notification count
     */
    private function getUnreadNotificationCount(int $userId): int
    {
        // This would be enhanced with a proper notifications table
        return 3;
    }
}
