<?php

namespace App\Interfaces\Repositories;

interface MetricsRepositoryInterface
{
    public function storeDailyMetric(array $data);
    
    public function storeSalesAnalytic(array $data);
    
    public function storeProductionMetric(array $data);
    
    public function storeSystemMetric(array $data);
    
    public function getMetricsByPeriod(string $table, string $startDate, string $endDate, array $conditions = []);
    
    public function getLatestMetrics(string $table, int $limit = 10);
    
    public function calculateGrowth(string $table, string $field, string $currentPeriod, string $previousPeriod);
    
    public function getAggregatedMetrics(string $table, array $fields, string $groupBy, string $period);
}
