<?php

namespace App\Services;

use App\Interfaces\Repositories\SalesRepositoryInterface;
use App\Interfaces\Services\SalesAnalyticsServiceInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SalesAnalyticsService implements SalesAnalyticsServiceInterface
{
    /**
     * Cache TTL for analytics data (15 minutes)
     */
    private const ANALYTICS_CACHE_TTL = 900;

    protected SalesRepositoryInterface $salesRepository;

    public function __construct(SalesRepositoryInterface $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    /**
     * Get sales trend data formatted for charts
     */
    public function getSalesTrendsByTimeRange(string $timeRange, string $startDate, string $endDate, array $filters = []): array
    {
        $cacheKey = "sales_trends:{$timeRange}:{$startDate}:{$endDate}:" . md5(json_encode($filters));

        return Cache::remember($cacheKey, self::ANALYTICS_CACHE_TTL, function () use ($timeRange, $startDate, $endDate, $filters) {
            try {
                // Get time range configuration
                $config = $this->getTimeRangeConfig($timeRange);

                // Parse dates based on configuration
                $startDateCarbon = Carbon::parse($startDate)->{$config['startDateMethod']}();
                $endDateCarbon = Carbon::parse($endDate)->{$config['endDateMethod']}();

                // Get sales orders based on filters
                $orders = empty($filters)
                    ? $this->salesRepository->getProductionManagerOrders($startDateCarbon, $endDateCarbon)
                    : $this->salesRepository->getOrdersByDateRange($filters, $startDateCarbon, $endDateCarbon);

                // Group orders by the specified format
                $groupedOrders = $orders->groupBy(function($order) use ($config) {
                    return $config['groupByCallback']($order->created_at);
                });

                // Generate period keys
                $periodKeys = collect(CarbonPeriod::create(
                        $startDateCarbon,
                        $config['periodInterval'],
                        $endDateCarbon
                    ))->map($config['periodKeyCallback'])->toArray();

                // Build chart data
                return $this->buildChartData(
                    $groupedOrders,
                    $periodKeys,
                    $config['labelFormatter']
                );
            } catch (\Exception $e) {
                Log::error('SalesAnalyticsService Error:', [
                    'method' => 'getSalesTrendsByTimeRange',
                    'error' => $e->getMessage(),
                    'timeRange' => $timeRange,
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);

                return $this->getDefaultSalesData($e->getMessage());
            }
        });
    }

    /**
     * Get sales overview with key metrics
     */
    public function getSalesOverview(Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $cacheKey = "sales_overview:{$startDate->format('Y-m-d')}:{$endDate->format('Y-m-d')}:" . md5(json_encode($filters));

        return Cache::remember($cacheKey, self::ANALYTICS_CACHE_TTL, function () use ($startDate, $endDate, $filters) {
            $orders = empty($filters)
                ? $this->salesRepository->getProductionManagerOrders($startDate, $endDate)
                : $this->salesRepository->getOrdersByDateRange($filters, $startDate, $endDate);

            $totalSales = $orders->sum('price');
            $totalOrders = $orders->count();
            $avgOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

            return [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_order_value' => $avgOrderValue,
                'period_start' => $startDate->toDateString(),
                'period_end' => $endDate->toDateString(),
                'metrics' => [
                    'daily_average' => $this->calculateDailyAverage($orders, $startDate, $endDate),
                    'top_seller' => $this->getTopSeller($orders),
                ]
            ];
        });
    }

    /**
     * Get time range configuration for different periods
     */
    public function getTimeRangeConfig(string $timeRange): array
    {
        return match($timeRange) {
            'day' => [
                'startDateMethod' => 'startOfDay',
                'endDateMethod' => 'endOfDay',
                'groupByCallback' => fn($date) => $date->format('Y-m-d'),
                'periodInterval' => '1 day',
                'periodKeyCallback' => fn($date) => $date->format('Y-m-d'),
                'labelFormatter' => fn($date) => Carbon::parse($date)->format('M d')
            ],
            'week' => [
                'startDateMethod' => 'startOfWeek',
                'endDateMethod' => 'endOfWeek',
                'groupByCallback' => fn($date) => $date->format('Y') . '-W' . $date->format('W'),
                'periodInterval' => '1 week',
                'periodKeyCallback' => fn($date) => $date->format('Y') . '-W' . $date->format('W'),
                'labelFormatter' => fn($weekKey) => 'Week ' . explode('-W', $weekKey)[1]
            ],
            'month' => [
                'startDateMethod' => 'startOfMonth',
                'endDateMethod' => 'endOfMonth',
                'groupByCallback' => fn($date) => $date->format('Y-m'),
                'periodInterval' => '1 month',
                'periodKeyCallback' => fn($date) => $date->format('Y-m'),
                'labelFormatter' => fn($monthKey) => Carbon::createFromFormat('Y-m', $monthKey)->format('M Y')
            ],
            default => [
                'startDateMethod' => 'startOfDay',
                'endDateMethod' => 'endOfDay',
                'groupByCallback' => fn($date) => $date->format('Y-m-d'),
                'periodInterval' => '1 day',
                'periodKeyCallback' => fn($date) => $date->format('Y-m-d'),
                'labelFormatter' => fn($date) => Carbon::parse($date)->format('M d')
            ]
        };
    }

    /**
     * Get default empty sales data structure
     */
    public function getDefaultSalesData(?string $error = null): array
    {
        return [
            'data' => [],
            'categories' => [],
            'total_sales' => 0,
            'total_orders' => 0,
            'average_order_value' => 0,
            'error' => $error
        ];
    }

    /**
     * Build chart data from grouped orders
     */
    private function buildChartData(Collection $groupedOrders, array $periodKeys, callable $labelFormatter): array
    {
        $chartData = [];
        $categories = [];
        $totalSales = 0;
        $totalOrders = 0;

        foreach ($periodKeys as $periodKey) {
            $periodOrders = $groupedOrders->get($periodKey, collect());
            $periodSales = $periodOrders->sum('price');
            $periodOrderCount = $periodOrders->count();

            $label = $labelFormatter($periodKey);
            $categories[] = $label;

            // Format for ApexCharts (expected by frontend)
            $chartData[] = [
                'x' => $label,
                'y' => round($periodSales, 2)
            ];

            $totalSales += $periodSales;
            $totalOrders += $periodOrderCount;
        }

        return [
            'data' => $chartData,
            'categories' => $categories,
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0
        ];
    }

    /**
     * Calculate daily average sales
     */
    private function calculateDailyAverage(Collection $orders, Carbon $startDate, Carbon $endDate): float
    {
        $totalSales = $orders->sum('price');
        $daysDiff = $startDate->diffInDays($endDate) + 1; // Include both start and end dates

        return $daysDiff > 0 ? round($totalSales / $daysDiff, 2) : 0;
    }

    /**
     * Get top seller information
     */
    private function getTopSeller(Collection $orders): array
    {
        if ($orders->isEmpty()) {
            return ['name' => 'N/A', 'sales' => 0, 'orders' => 0];
        }

        $sellerSales = $orders->groupBy('seller_id')->map(function ($sellerOrders) {
            return [
                'name' => $sellerOrders->first()->seller->name ?? 'Unknown',
                'sales' => $sellerOrders->sum('price'),
                'orders' => $sellerOrders->count()
            ];
        });

        return $sellerSales->sortByDesc('sales')->first();
    }

    /**
     * Clear analytics cache
     */
    public function clearAnalyticsCache(): void
    {
        // Clear pattern-based cache keys (if using Redis or similar)
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            Cache::getStore()->getRedis()->del(Cache::getStore()->getRedis()->keys('sales_trends:*'));
            Cache::getStore()->getRedis()->del(Cache::getStore()->getRedis()->keys('sales_overview:*'));
        }
    }
}
