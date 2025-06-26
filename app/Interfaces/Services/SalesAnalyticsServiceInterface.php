<?php

namespace App\Interfaces\Services;

use Carbon\Carbon;

interface SalesAnalyticsServiceInterface
{
    /**
     * Get sales trend data formatted for charts
     */
    public function getSalesTrendsByTimeRange(string $timeRange, string $startDate, string $endDate, array $filters = []): array;

    /**
     * Get sales overview with key metrics
     */
    public function getSalesOverview(Carbon $startDate, Carbon $endDate, array $filters = []): array;

    /**
     * Get time range configuration for different periods
     */
    public function getTimeRangeConfig(string $timeRange): array;

    /**
     * Get default empty sales data structure
     */
    public function getDefaultSalesData(?string $error = null): array;
}
