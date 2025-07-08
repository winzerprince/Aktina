<?php

namespace App\Repositories;

use App\Repositories\MLDataRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MLRepository
{
    protected $mlDataRepository;
    protected $apiUrl;

    public function __construct(MLDataRepository $mlDataRepository, $apiUrl = null)
    {
        $this->mlDataRepository = $mlDataRepository;
        $this->apiUrl = $apiUrl ?? config('services.ml_microservice.url', 'http://localhost:8000');
    }

    /**
     * Get customer segments from ML microservice
     *
     * @return array|null
     */
    public function getCustomerSegments(): ?array
    {
        try {
            // Get retailer demographics data
            $retailers = $this->mlDataRepository->getRetailerDemographicsData();

            if ($retailers->isEmpty()) {
                Log::warning('No retailer data available for segmentation');
                return null;
            }

            // Send data to ML microservice
            $response = Http::timeout(30)->post("{$this->apiUrl}/segment-customers", [
                'retailers' => $retailers->toArray()
            ]);

            if (!$response->successful()) {
                Log::error('ML microservice error: ' . $response->body());
                return null;
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Error in getCustomerSegments: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get sales forecast from ML microservice
     *
     * @param int $horizonDays Number of days to forecast
     * @return array|null
     */
    public function getSalesForecast(int $horizonDays = 90): ?array
    {
        try {
            // Get Aktina sales data
            $salesData = $this->mlDataRepository->getAktinaSalesData(365); // Last year of data

            if ($salesData->isEmpty()) {
                Log::warning('No sales data available for forecasting');
                return null;
            }

            // Send data to ML microservice
            $response = Http::timeout(30)->post("{$this->apiUrl}/predict-sales", [
                'sales' => $salesData->toArray(),
                'horizon_days' => $horizonDays
            ]);

            if (!$response->successful()) {
                Log::error('ML microservice error: ' . $response->body());
                return null;
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Error in getSalesForecast: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if ML microservice is healthy
     *
     * @return bool
     */
    public function isServiceHealthy(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/health");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('ML microservice health check failed: ' . $e->getMessage());
            return false;
        }
    }
}
