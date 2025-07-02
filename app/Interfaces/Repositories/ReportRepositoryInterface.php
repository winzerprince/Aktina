<?php

namespace App\Interfaces\Repositories;

use Carbon\Carbon;

interface ReportRepositoryInterface
{
    public function generateInventoryReport(Carbon $startDate, Carbon $endDate): array;
    public function generateSalesReport(Carbon $startDate, Carbon $endDate): array;
    public function generateOrderReport(Carbon $startDate, Carbon $endDate): array;
    public function generateUserActivityReport(Carbon $startDate, Carbon $endDate): array;
    public function generateFinancialReport(Carbon $startDate, Carbon $endDate): array;
    public function getCustomReportData(array $parameters): array;
    public function exportReportData(array $data, string $format = 'csv'): string;
}
