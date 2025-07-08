<?php

namespace App\Services;

use App\Repositories\MLRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MLService
{
    protected $mlRepository;
    protected $cacheTtl = 86400; // 24 hours

    public function __construct(MLRepository $mlRepository)
    {
        $this->mlRepository = $mlRepository;
    }

    /**
     * Get customer segments with caching
     *
     * @return array
     */
    public function getCustomerSegments(): array
    {
        return Cache::remember('ml_customer_segments', $this->cacheTtl, function () {
            $segments = $this->mlRepository->getCustomerSegments();

            if (!$segments) {
                Log::warning('Failed to get customer segments from ML microservice');
                return [
                    'retailer_segments' => [],
                    'segment_descriptions' => [],
                    'segment_distribution' => []
                ];
            }

            return $segments;
        });
    }

    /**
     * Get sales forecast with caching
     *
     * @param int $horizonDays
     * @return array
     */
    public function getSalesForecast(int $horizonDays = 90): array
    {
        $cacheKey = "ml_sales_forecast_{$horizonDays}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($horizonDays) {
            $forecast = $this->mlRepository->getSalesForecast($horizonDays);

            if (!$forecast) {
                Log::warning('Failed to get sales forecast from ML microservice');
                return [
                    'forecast_dates' => [],
                    'forecast_values' => [],
                    'forecast_lower_bound' => [],
                    'forecast_upper_bound' => []
                ];
            }

            return $forecast;
        });
    }

    /**
     * Get customer segment chart data
     *
     * @return array
     */
    public function getCustomerSegmentChartData(): array
    {
        $segments = $this->getCustomerSegments();

        if (empty($segments['segment_distribution'])) {
            return [
                'labels' => [],
                'series' => []
            ];
        }

        $descriptions = $segments['segment_descriptions'] ?? [];
        $distribution = $segments['segment_distribution'] ?? [];

        $labels = [];
        $series = [];

        foreach ($distribution as $segmentId => $count) {
            $label = $descriptions[$segmentId] ?? "Segment {$segmentId}";
            $labels[] = $label;
            $series[] = $count;
        }

        return [
            'labels' => $labels,
            'series' => $series
        ];
    }

    /**
     * Get sales forecast chart data
     *
     * @param int $horizonDays
     * @return array
     */
    public function getSalesForecastChartData(int $horizonDays = 90): array
    {
        $forecast = $this->getSalesForecast($horizonDays);

        if (empty($forecast['forecast_dates'])) {
            return [
                'dates' => [],
                'values' => [],
                'lower' => [],
                'upper' => []
            ];
        }

        return [
            'dates' => $forecast['forecast_dates'],
            'values' => $forecast['forecast_values'],
            'lower' => $forecast['forecast_lower_bound'],
            'upper' => $forecast['forecast_upper_bound']
        ];
    }

    /**
     * Check if ML microservice is healthy
     *
     * @return bool
     */
    public function isServiceHealthy(): bool
    {
        return Cache::remember('ml_service_health', 300, function () {
            return $this->mlRepository->isServiceHealthy();
        });
    }

    /**
     * Clear ML data cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('ml_customer_segments');
        Cache::forget('ml_service_health');

        // Clear forecast caches for different horizons
        foreach ([30, 60, 90, 180, 365] as $days) {
            Cache::forget("ml_sales_forecast_{$days}");
        }
    }
}
