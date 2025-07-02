<?php

namespace App\Services;

use App\Interfaces\Services\MetricsServiceInterface;
use App\Interfaces\Repositories\MetricsRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class MetricsService implements MetricsServiceInterface
{
    public function __construct(
        private MetricsRepositoryInterface $metricsRepository
    ) {}

    public function generateDailyMetrics()
    {
        $date = Carbon::today();
        
        return $this->metricsRepository->storeDailyMetrics([
            'date' => $date,
            'total_orders' => $this->getTotalOrdersForDate($date),
            'total_revenue' => $this->getTotalRevenueForDate($date),
            'active_users' => $this->getActiveUsersForDate($date),
            'inventory_value' => $this->getInventoryValueForDate($date),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function getKPIData(array $kpis = [], Carbon $startDate = null, Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::today()->subDays(30);
        $endDate = $endDate ?? Carbon::today();
        
        $cacheKey = "kpi_data_" . implode('_', $kpis) . "_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 3600, function () use ($kpis, $startDate, $endDate) {
            return $this->metricsRepository->getKPIData($kpis, $startDate, $endDate);
        });
    }

    public function getPerformanceMetrics(string $period = 'daily'): array
    {
        $cacheKey = "performance_metrics_{$period}";
        
        return Cache::remember($cacheKey, 1800, function () use ($period) {
            return $this->metricsRepository->getPerformanceMetrics($period);
        });
    }

    public function getComparisonData(Carbon $startDate, Carbon $endDate, Carbon $compareStartDate, Carbon $compareEndDate): array
    {
        $current = $this->metricsRepository->getMetricsForPeriod($startDate, $endDate);
        $comparison = $this->metricsRepository->getMetricsForPeriod($compareStartDate, $compareEndDate);
        
        return [
            'current' => $current,
            'comparison' => $comparison,
            'variance' => $this->calculateVariance($current, $comparison)
        ];
    }

    public function getTrendData(array $metrics, string $period = 'daily', int $periods = 30): array
    {
        $cacheKey = "trend_data_" . implode('_', $metrics) . "_{$period}_{$periods}";
        
        return Cache::remember($cacheKey, 1800, function () use ($metrics, $period, $periods) {
            return $this->metricsRepository->getTrendData($metrics, $period, $periods);
        });
    }

    private function getTotalOrdersForDate(Carbon $date): int
    {
        return $this->metricsRepository->getOrderCountForDate($date);
    }

    private function getTotalRevenueForDate(Carbon $date): float
    {
        return $this->metricsRepository->getRevenueForDate($date);
    }

    private function getActiveUsersForDate(Carbon $date): int
    {
        return $this->metricsRepository->getActiveUsersForDate($date);
    }

    private function getInventoryValueForDate(Carbon $date): float
    {
        return $this->metricsRepository->getInventoryValueForDate($date);
    }

    private function calculateVariance(array $current, array $comparison): array
    {
        $variance = [];
        
        foreach ($current as $key => $value) {
            if (isset($comparison[$key]) && is_numeric($value) && is_numeric($comparison[$key])) {
                $comparisonValue = $comparison[$key];
                if ($comparisonValue != 0) {
                    $variance[$key] = (($value - $comparisonValue) / $comparisonValue) * 100;
                } else {
                    $variance[$key] = $value > 0 ? 100 : 0;
                }
            }
        }
        
        return $variance;
    }
}
