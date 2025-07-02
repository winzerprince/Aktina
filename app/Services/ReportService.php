<?php

namespace App\Services;

use App\Interfaces\Services\ReportServiceInterface;
use App\Interfaces\Repositories\ReportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ReportService implements ReportServiceInterface
{
    public function __construct(
        private ReportRepositoryInterface $reportRepository
    ) {}

    public function generateInventoryReport(Carbon $startDate = null, Carbon $endDate = null, string $format = 'array'): array|string
    {
        $startDate = $startDate ?? Carbon::today()->subDays(30);
        $endDate = $endDate ?? Carbon::today();
        
        $cacheKey = "inventory_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        $data = Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return $this->reportRepository->generateInventoryReport($startDate, $endDate);
        });
        
        if ($format === 'export') {
            return $this->reportRepository->exportReportData($this->flattenReportData($data));
        }
        
        return $data;
    }

    public function generateSalesReport(Carbon $startDate = null, Carbon $endDate = null, string $format = 'array'): array|string
    {
        $startDate = $startDate ?? Carbon::today()->subDays(30);
        $endDate = $endDate ?? Carbon::today();
        
        $cacheKey = "sales_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        $data = Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return $this->reportRepository->generateSalesReport($startDate, $endDate);
        });
        
        if ($format === 'export') {
            return $this->reportRepository->exportReportData($this->flattenReportData($data));
        }
        
        return $data;
    }

    public function generateOrderReport(Carbon $startDate = null, Carbon $endDate = null, string $format = 'array'): array|string
    {
        $startDate = $startDate ?? Carbon::today()->subDays(30);
        $endDate = $endDate ?? Carbon::today();
        
        $cacheKey = "order_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        $data = Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return $this->reportRepository->generateOrderReport($startDate, $endDate);
        });
        
        if ($format === 'export') {
            return $this->reportRepository->exportReportData($this->flattenReportData($data));
        }
        
        return $data;
    }

    public function generateUserActivityReport(Carbon $startDate = null, Carbon $endDate = null, string $format = 'array'): array|string
    {
        $startDate = $startDate ?? Carbon::today()->subDays(30);
        $endDate = $endDate ?? Carbon::today();
        
        $cacheKey = "user_activity_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        $data = Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return $this->reportRepository->generateUserActivityReport($startDate, $endDate);
        });
        
        if ($format === 'export') {
            return $this->reportRepository->exportReportData($this->flattenReportData($data));
        }
        
        return $data;
    }

    public function generateFinancialReport(Carbon $startDate = null, Carbon $endDate = null, string $format = 'array'): array|string
    {
        $startDate = $startDate ?? Carbon::today()->subDays(30);
        $endDate = $endDate ?? Carbon::today();
        
        $cacheKey = "financial_report_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        $data = Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return $this->reportRepository->generateFinancialReport($startDate, $endDate);
        });
        
        if ($format === 'export') {
            return $this->reportRepository->exportReportData($this->flattenReportData($data));
        }
        
        return $data;
    }

    public function generateCustomReport(array $parameters, string $format = 'array'): array|string
    {
        $data = $this->reportRepository->getCustomReportData($parameters);
        
        if ($format === 'export') {
            return $this->reportRepository->exportReportData($data);
        }
        
        return $data;
    }

    public function getAvailableReports(): array
    {
        return [
            'inventory' => [
                'name' => 'Inventory Report',
                'description' => 'Comprehensive inventory analysis including stock levels, movements, and alerts',
                'parameters' => ['start_date', 'end_date', 'format']
            ],
            'sales' => [
                'name' => 'Sales Report',
                'description' => 'Sales performance analysis including revenue, trends, and top products',
                'parameters' => ['start_date', 'end_date', 'format']
            ],
            'orders' => [
                'name' => 'Order Report',
                'description' => 'Order analysis including status breakdown and user activity',
                'parameters' => ['start_date', 'end_date', 'format']
            ],
            'user_activity' => [
                'name' => 'User Activity Report',
                'description' => 'User engagement and activity analysis',
                'parameters' => ['start_date', 'end_date', 'format']
            ],
            'financial' => [
                'name' => 'Financial Report',
                'description' => 'Financial overview including revenue, costs, and margins',
                'parameters' => ['start_date', 'end_date', 'format']
            ],
            'custom' => [
                'name' => 'Custom Report',
                'description' => 'Build custom reports with flexible parameters',
                'parameters' => ['table', 'joins', 'where', 'select', 'group_by', 'order_by', 'limit', 'format']
            ]
        ];
    }

    public function scheduleReport(array $reportConfig, string $schedule): bool
    {
        // TODO: Implement report scheduling with Laravel's task scheduler
        return true;
    }

    private function flattenReportData(array $data): array
    {
        $flattened = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value) && !empty($value)) {
                if (isset($value[0]) && is_object($value[0])) {
                    // Handle collections
                    foreach ($value as $item) {
                        $flattened[] = (array) $item;
                    }
                } elseif (is_array($value)) {
                    // Handle associative arrays
                    $row = ['section' => $key];
                    foreach ($value as $subKey => $subValue) {
                        $row[$subKey] = is_scalar($subValue) ? $subValue : json_encode($subValue);
                    }
                    $flattened[] = $row;
                }
            }
        }
        
        return $flattened;
    }
}
