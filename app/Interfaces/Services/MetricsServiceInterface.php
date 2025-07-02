<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface MetricsServiceInterface
{
    public function calculateDailyMetrics(string $date);
    
    public function calculateSalesAnalytics(User $user, string $date);
    
    public function calculateProductionMetrics(string $date);
    
    public function calculateSystemMetrics(string $date);
    
    public function getKPIs(User $user, string $period = '30days');
    
    public function getGrowthMetrics(string $metricType, string $period = '30days');
    
    public function getComparisonMetrics(string $currentPeriod, string $previousPeriod);
    
    public function aggregateMetrics(string $period, array $metrics);
    
    public function getPerformanceScore(User $user, string $period = '30days');
}
