<?php

namespace App\Services;

use App\Models\ResourceOrder;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    public function getSupplierStats(): array
    {
        return Cache::remember('supplier_stats', 300, function () {
            $totalOrders = ResourceOrder::count();
            $pendingOrders = ResourceOrder::where('status', 'pending')->count();
            $completedOrders = ResourceOrder::where('status', 'completed')->count();
            $totalRevenue = ResourceOrder::where('status', 'completed')->sum('total_cost');

            return [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
                'fulfillment_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
                'total_revenue' => $totalRevenue,
                'average_order_value' => $completedOrders > 0 ? $totalRevenue / $completedOrders : 0,
            ];
        });
    }

    public function getRecentOrders(int $limit = 10)
    {
        return ResourceOrder::with(['resource', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOrderTrends(int $days = 30): array
    {
        return Cache::remember("supplier_order_trends_{$days}", 300, function () use ($days) {
            $orders = ResourceOrder::where('created_at', '>=', now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_cost) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'dates' => $orders->pluck('date'),
                'orders' => $orders->pluck('count'),
                'revenue' => $orders->pluck('revenue'),
            ];
        });
    }

    public function getResourceSupplyMetrics(): array
    {
        return Cache::remember('resource_supply_metrics', 300, function () {
            $totalResources = Resource::count();
            $lowStockResources = Resource::where('quantity', '<=', 10)->count();
            $outOfStockResources = Resource::where('quantity', '<=', 0)->count();

            $totalInventoryValue = Resource::sum(DB::raw('quantity * unit_cost'));
            
            return [
                'total_resources' => $totalResources,
                'low_stock_resources' => $lowStockResources,
                'out_of_stock_resources' => $outOfStockResources,
                'total_inventory_value' => $totalInventoryValue,
                'stock_health_score' => $this->calculateStockHealthScore(),
            ];
        });
    }

    public function getTopRequestedResources(int $limit = 10)
    {
        return Cache::remember("top_requested_resources_{$limit}", 300, function () use ($limit) {
            return DB::table('resource_orders')
                ->join('resources', 'resource_orders.resource_id', '=', 'resources.id')
                ->selectRaw('resources.name, resources.unit, SUM(resource_orders.quantity) as total_ordered, SUM(resource_orders.total_cost) as total_value')
                ->where('resource_orders.created_at', '>=', now()->subDays(30))
                ->groupBy('resources.id', 'resources.name', 'resources.unit')
                ->orderBy('total_ordered', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getSupplyPerformanceMetrics(): array
    {
        return Cache::remember('supply_performance_metrics', 300, function () {
            $thisMonth = ResourceOrder::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);

            $lastMonth = ResourceOrder::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year);

            $thisMonthOrders = $thisMonth->count();
            $lastMonthOrders = $lastMonth->count();
            $thisMonthRevenue = $thisMonth->sum('total_cost');
            $lastMonthRevenue = $lastMonth->sum('total_cost');

            $avgDeliveryTime = $this->getAverageDeliveryTime();
            $onTimeDeliveryRate = $this->getOnTimeDeliveryRate();

            return [
                'orders_growth' => $lastMonthOrders > 0 
                    ? round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 2)
                    : 0,
                'revenue_growth' => $lastMonthRevenue > 0 
                    ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2)
                    : 0,
                'avg_delivery_time' => $avgDeliveryTime,
                'on_time_delivery_rate' => $onTimeDeliveryRate,
            ];
        });
    }

    public function getOrdersByStatus(): array
    {
        return Cache::remember('supplier_orders_by_status', 300, function () {
            return ResourceOrder::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    public function getResourceCategories(): array
    {
        return Cache::remember('resource_categories', 300, function () {
            return Resource::selectRaw('category, COUNT(*) as count, SUM(quantity * unit_cost) as total_value')
                ->whereNotNull('category')
                ->groupBy('category')
                ->get()
                ->map(function ($item) {
                    return [
                        'category' => $item->category,
                        'count' => $item->count,
                        'total_value' => $item->total_value,
                    ];
                })
                ->toArray();
        });
    }

    private function calculateStockHealthScore(): float
    {
        $totalResources = Resource::count();
        if ($totalResources === 0) return 0;

        $adequateStock = Resource::where('quantity', '>', 10)->count();
        $lowStock = Resource::whereBetween('quantity', [1, 10])->count();
        $outOfStock = Resource::where('quantity', '<=', 0)->count();

        // Weighted scoring: adequate stock gets full points, low stock gets partial, out of stock gets zero
        $score = (($adequateStock * 100) + ($lowStock * 50) + ($outOfStock * 0)) / $totalResources;

        return round($score, 1);
    }

    private function getAverageDeliveryTime(): float
    {
        $deliveredOrders = ResourceOrder::where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        if ($deliveredOrders->isEmpty()) {
            return 0;
        }

        $totalDays = $deliveredOrders->sum(function ($order) {
            return $order->created_at->diffInDays($order->delivered_at);
        });

        return round($totalDays / $deliveredOrders->count(), 1);
    }

    private function getOnTimeDeliveryRate(): float
    {
        $totalDeliveredOrders = ResourceOrder::where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($totalDeliveredOrders === 0) return 0;

        // Assume 3 days is the expected delivery time
        $onTimeOrders = ResourceOrder::where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereRaw('DATEDIFF(delivered_at, created_at) <= 3')
            ->count();

        return round(($onTimeOrders / $totalDeliveredOrders) * 100, 2);
    }
}
