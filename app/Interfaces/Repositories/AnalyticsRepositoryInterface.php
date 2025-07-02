<?php

namespace App\Interfaces\Repositories;

interface AnalyticsRepositoryInterface
{
    public function getDailyMetrics(string $startDate, string $endDate, string $role = null);
    
    public function getSalesAnalytics(int $userId = null, string $startDate = null, string $endDate = null);
    
    public function getProductionMetrics(string $startDate, string $endDate);
    
    public function getSystemMetrics(string $startDate, string $endDate);
    
    public function getOrderTrends(int $userId = null, string $period = '30days');
    
    public function getInventoryTrends(int $warehouseId = null, string $period = '30days');
    
    public function getUserActivityData(string $period = '30days');
    
    public function aggregateData(string $table, array $conditions, string $groupBy, array $aggregates);
}
