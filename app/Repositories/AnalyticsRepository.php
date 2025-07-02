<?php

namespace App\Repositories;

use App\Interfaces\Repositories\AnalyticsRepositoryInterface;
use App\Models\DailyMetric;
use App\Models\SalesAnalytic;
use App\Models\ProductionMetric;
use App\Models\SystemMetric;
use App\Models\Order;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsRepository implements AnalyticsRepositoryInterface
{
    public function getDailyMetrics(string $startDate, string $endDate, string $role = null)
    {
        $query = DailyMetric::whereBetween('date', [$startDate, $endDate]);
        
        if ($role) {
            $query->where('role', $role);
        }
        
        return $query->orderBy('date')->get();
    }
    
    public function getSalesAnalytics(int $userId = null, string $startDate = null, string $endDate = null)
    {
        $query = SalesAnalytic::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        
        return $query->orderBy('date')->get();
    }
    
    public function getProductionMetrics(string $startDate, string $endDate)
    {
        return ProductionMetric::whereBetween('date', [$startDate, $endDate])
                              ->orderBy('date')
                              ->get();
    }
    
    public function getSystemMetrics(string $startDate, string $endDate)
    {
        return SystemMetric::whereBetween('date', [$startDate, $endDate])
                          ->orderBy('date')
                          ->get();
    }
    
    public function getOrderTrends(int $userId = null, string $period = '30days')
    {
        $days = (int) filter_var($period, FILTER_SANITIZE_NUMBER_INT);
        $startDate = now()->subDays($days);
        
        $query = Order::where('created_at', '>=', $startDate)
                     ->select(
                         DB::raw('DATE(created_at) as date'),
                         DB::raw('COUNT(*) as order_count'),
                         DB::raw('SUM(total_price) as total_revenue'),
                         DB::raw('AVG(total_price) as average_order_value')
                     )
                     ->groupBy('date');
        
        if ($userId) {
            $query->where('seller_id', $userId);
        }
        
        return $query->orderBy('date')->get();
    }
    
    public function getInventoryTrends(int $warehouseId = null, string $period = '30days')
    {
        $days = (int) filter_var($period, FILTER_SANITIZE_NUMBER_INT);
        $startDate = now()->subDays($days);
        
        $query = Resource::select(
            'name',
            'units',
            'available_quantity',
            'reserved_quantity',
            'reorder_level',
            'overstock_level',
            'last_movement_at'
        );
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        return $query->get();
    }
    
    public function getUserActivityData(string $period = '30days')
    {
        $days = (int) filter_var($period, FILTER_SANITIZE_NUMBER_INT);
        $startDate = now()->subDays($days);
        
        return User::select(
            DB::raw('DATE(last_login_at) as date'),
            DB::raw('COUNT(*) as active_users')
        )
        ->where('last_login_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }
    
    public function aggregateData(string $table, array $conditions, string $groupBy, array $aggregates)
    {
        $query = DB::table($table);
        
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
        
        $selectFields = [$groupBy];
        foreach ($aggregates as $alias => $aggregate) {
            $selectFields[] = DB::raw("$aggregate as $alias");
        }
        
        return $query->select($selectFields)
                    ->groupBy($groupBy)
                    ->get();
    }
}
