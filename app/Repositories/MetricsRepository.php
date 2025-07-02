<?php

namespace App\Repositories;

use App\Interfaces\Repositories\MetricsRepositoryInterface;
use App\Models\DailyMetric;
use App\Models\SalesAnalytic;
use App\Models\ProductionMetric;
use App\Models\SystemMetric;
use Illuminate\Support\Facades\DB;

class MetricsRepository implements MetricsRepositoryInterface
{
    public function storeDailyMetric(array $data)
    {
        return DailyMetric::updateOrCreate(
            [
                'date' => $data['date'],
                'role' => $data['role'],
                'metric_type' => $data['metric_type'],
                'metric_name' => $data['metric_name'],
                'user_id' => $data['user_id'] ?? null,
            ],
            $data
        );
    }
    
    public function storeSalesAnalytic(array $data)
    {
        return SalesAnalytic::updateOrCreate(
            [
                'date' => $data['date'],
                'user_id' => $data['user_id'],
            ],
            $data
        );
    }
    
    public function storeProductionMetric(array $data)
    {
        return ProductionMetric::updateOrCreate(
            ['date' => $data['date']],
            $data
        );
    }
    
    public function storeSystemMetric(array $data)
    {
        return SystemMetric::updateOrCreate(
            ['date' => $data['date']],
            $data
        );
    }
    
    public function getMetricsByPeriod(string $table, string $startDate, string $endDate, array $conditions = [])
    {
        $query = DB::table($table)->whereBetween('date', [$startDate, $endDate]);
        
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }
        
        return $query->orderBy('date')->get();
    }
    
    public function getLatestMetrics(string $table, int $limit = 10)
    {
        return DB::table($table)
                 ->orderBy('date', 'desc')
                 ->limit($limit)
                 ->get();
    }
    
    public function calculateGrowth(string $table, string $field, string $currentPeriod, string $previousPeriod)
    {
        $current = DB::table($table)
                    ->where('date', $currentPeriod)
                    ->avg($field);
                    
        $previous = DB::table($table)
                     ->where('date', $previousPeriod)
                     ->avg($field);
        
        if ($previous > 0) {
            return (($current - $previous) / $previous) * 100;
        }
        
        return 0;
    }
    
    public function getAggregatedMetrics(string $table, array $fields, string $groupBy, string $period)
    {
        $startDate = now()->subDays((int) filter_var($period, FILTER_SANITIZE_NUMBER_INT));
        
        $selectFields = [$groupBy];
        foreach ($fields as $alias => $field) {
            $selectFields[] = DB::raw("$field as $alias");
        }
        
        return DB::table($table)
                 ->select($selectFields)
                 ->where('date', '>=', $startDate)
                 ->groupBy($groupBy)
                 ->get();
    }
}
