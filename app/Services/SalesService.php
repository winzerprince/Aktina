<?php

namespace App\Services;

use App\Repositories\SalesRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * SalesService to handle business logic related to sales.
 * It uses SalesRepository to interact with the database and then
 * processes or formats the data.
 */
class SalesService
{
    protected SalesRepository $salesRepository;

    /**
     * SalesService constructor.
     *
     * @param SalesRepository $salesRepository
     */
    public function __construct(SalesRepository $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    /**
     * Get a sales summary for a company.
     * This method adds a caching layer on top of the repository call.
     *
     * @param string $companyName
     * @return array
     */
    public function getSalesSummary(string $companyName): array
    {
        // Define a unique cache key for this company's summary
        $cacheKey = 'sales_summary_' . str_replace(' ', '_', $companyName);

        // Cache the result for 60 minutes to improve performance
        return Cache::remember($cacheKey, 3600, function () use ($companyName) {
            return $this->salesRepository->getSalesSummary($companyName);
        });
    }

    /**
     * Generates a comprehensive sales report for a company.
     * This method demonstrates orchestration by calling multiple repository methods.
     *
     * @param string $companyName
     * @return array
     */
    public function generateSalesReport(string $companyName, string $startDate, string $endDate ): array
    {
        // 1. Get the overall summary
        $summary = $this->salesRepository->getSalesSummary($companyName);

        // 2. Get sales from the last 30 days
        $startDate = Carbon::now()->subDays(30)->toDateTimeString();
        $endDate = Carbon::now()->toDateTimeString();
        $recentSales = $this->salesRepository->getSales($companyName, $startDate, $endDate);

        // 3. Combine the data into a single report
        return [
            'company_name' => $companyName,
            'summary' => $summary,
            'recent_sales_count' => $recentSales->count(),
            'recent_sales_period' => 'Last 30 days',
            'report_generated_at' => Carbon::now()->toIso8601String(),
        ];
    }/**
 * Get sales data formatted for a chart (e.g., ApexCharts).
 *
 * @param string $companyName
 * @param string $startDate
 * @param string $endDate
 * @return array
 */
    public function getSalesDataForChart(string $companyName, string $startDate, string $endDate): array
    {
        $salesData = $this->salesRepository->getSalesGroupedByDay($companyName, $startDate, $endDate);

        // Create a map of date => total_sales for easy lookup
        $salesMap = $salesData->keyBy('date')->map(function ($item) {
            return $item->total_sales;
        });

        $categories = [];
        $seriesData = [];

        // Iterate through the date range to build a continuous dataset
        $period = Carbon::parse($startDate)->toPeriod(Carbon::parse($endDate));
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $categories[] = $formattedDate;
            // Use the sales total if it exists, otherwise default to 0
            $seriesData[] = $salesMap[$formattedDate] ?? 0;
        }

        return [
            'series' => [
                [
                    'name' => 'Daily Sales',
                    'data' => $seriesData,
                ],
            ],
            'categories' => $categories,
        ];
    }

    /**
     * Get paginated sales data for a company.
     *
     * @param string $companyName
     * @param string $startDate
     * @param string $endDate
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSalesForTable(string $companyName, string $startDate, string $endDate, int $perPage = 15)
    {
        return $this->salesRepository->getSales($companyName, $startDate, $endDate, $perPage);
    }


}
