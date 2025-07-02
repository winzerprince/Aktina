<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface AnalyticsServiceInterface
{
    public function getDashboardData(User $user, string $period = '30days');
    
    public function getSalesTrends(User $user = null, string $period = '30days');
    
    public function getInventoryAnalytics(int $warehouseId = null, string $period = '30days');
    
    public function getProductionMetrics(string $period = '30days');
    
    public function getSystemMetrics(string $period = '30days');
    
    public function getUserGrowthData(string $period = '30days');
    
    public function getOrderAnalytics(User $user = null, string $period = '30days');
    
    public function generateChartData(string $type, array $data, array $options = []);
    
    public function cacheAnalyticsData(string $key, $data, int $ttl = 3600);
    
    public function getCachedAnalyticsData(string $key);
}
