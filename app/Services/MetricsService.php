<?php

namespace App\Services;

use App\Interfaces\Services\MetricsServiceInterface;
use App\Interfaces\Repositories\MetricsRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

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

    public function calculateDailyMetrics(string $date)
    {
        $dateObj = Carbon::parse($date);
        
        return [
            'date' => $date,
            'total_orders' => $this->getTotalOrdersForDate($dateObj),
            'total_revenue' => $this->getTotalRevenueForDate($dateObj),
            'active_users' => $this->getActiveUsersForDate($dateObj),
            'inventory_value' => $this->getInventoryValueForDate($dateObj),
            'growth_rate' => $this->calculateGrowthRate($dateObj),
        ];
    }

    public function calculateSalesAnalytics(User $user, string $date)
    {
        $dateObj = Carbon::parse($date);
        
        return Cache::remember("sales_analytics_{$user->id}_{$date}", 3600, function () use ($user, $dateObj) {
            return $this->metricsRepository->getSalesAnalyticsForUser($user, $dateObj);
        });
    }

    public function calculateProductionMetrics(string $date)
    {
        $dateObj = Carbon::parse($date);
        
        return Cache::remember("production_metrics_{$date}", 3600, function () use ($dateObj) {
            return $this->metricsRepository->getProductionMetricsForDate($dateObj);
        });
    }

    public function calculateSystemMetrics(string $date)
    {
        $dateObj = Carbon::parse($date);
        
        return [
            'date' => $date,
            'system_uptime' => 99.9, // Simplified
            'response_time' => $this->getAverageResponseTime($dateObj),
            'error_rate' => $this->getErrorRate($dateObj),
            'database_performance' => $this->getDatabasePerformance($dateObj),
        ];
    }

    public function getKPIs(User $user, string $period = '30days')
    {
        $cacheKey = "kpis_{$user->id}_{$period}";
        
        return Cache::remember($cacheKey, 1800, function () use ($user, $period) {
            return $this->metricsRepository->getKPIsForUser($user, $period);
        });
    }

    public function getGrowthMetrics(string $metricType, string $period = '30days')
    {
        $cacheKey = "growth_metrics_{$metricType}_{$period}";
        
        return Cache::remember($cacheKey, 3600, function () use ($metricType, $period) {
            return $this->metricsRepository->getGrowthMetrics($metricType, $period);
        });
    }

    public function getComparisonMetrics(string $currentPeriod, string $previousPeriod)
    {
        $current = $this->metricsRepository->getMetricsForPeriod(
            Carbon::parse($currentPeriod . ' ago')->startOfDay(),
            Carbon::today()
        );
        
        $previous = $this->metricsRepository->getMetricsForPeriod(
            Carbon::parse($previousPeriod . ' ago')->startOfDay(),
            Carbon::parse($currentPeriod . ' ago')->startOfDay()
        );
        
        return [
            'current' => $current,
            'previous' => $previous,
            'variance' => $this->calculateVariance($current, $previous),
        ];
    }

    public function aggregateMetrics(string $period, array $metrics)
    {
        $cacheKey = "aggregate_metrics_{$period}_" . md5(serialize($metrics));
        
        return Cache::remember($cacheKey, 1800, function () use ($period, $metrics) {
            return $this->metricsRepository->aggregateMetrics($period, $metrics);
        });
    }

    public function getPerformanceScore(User $user, string $period = '30days')
    {
        $cacheKey = "performance_score_{$user->id}_{$period}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user, $period) {
            $metrics = $this->getKPIs($user, $period);
            
            // Calculate weighted performance score
            $weights = [
                'order_completion_rate' => 0.3,
                'response_time' => 0.2,
                'customer_satisfaction' => 0.25,
                'revenue_growth' => 0.25,
            ];
            
            $score = 0;
            foreach ($weights as $metric => $weight) {
                $score += ($metrics[$metric] ?? 0) * $weight;
            }
            
            return round($score, 2);
        });
    }

    // Helper methods for the new implementations
    private function calculateGrowthRate(Carbon $date): float
    {
        $currentValue = $this->getTotalRevenueForDate($date);
        $previousValue = $this->getTotalRevenueForDate($date->copy()->subDay());
        
        if ($previousValue == 0) return 0;
        
        return round((($currentValue - $previousValue) / $previousValue) * 100, 2);
    }

    private function getAverageResponseTime(Carbon $date): float
    {
        // Simplified - would get actual response times in real implementation
        return rand(50, 200) / 100; // 0.5 to 2.0 seconds
    }

    private function getErrorRate(Carbon $date): float
    {
        // Simplified - would get actual error rates
        return rand(0, 50) / 1000; // 0.0% to 0.05%
    }

    private function getDatabasePerformance(Carbon $date): array
    {
        return [
            'query_time' => rand(10, 100) / 1000, // 0.01 to 0.1 seconds
            'connection_pool' => rand(80, 100), // 80% to 100% efficiency
            'cache_hit_rate' => rand(85, 99), // 85% to 99%
        ];
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
