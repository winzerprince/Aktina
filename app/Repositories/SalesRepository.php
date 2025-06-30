<?php
namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * SalesRepository to handle sales-related data operations.
 * It contains the following methods:
 * - getSales: Retrieves all sales for a specific company with optional date filtering and pagination.
 * - getTotalSales: Calculates the total sales amount for a specific company.
 * - getSalesSummary: Provides a summary of sales for a specific company, including order count, total revenue, and average order value.
 * - getSalesGroupedByDay: Gets sales data grouped by day for chart visualization.
 */
class SalesRepository{

    /**
     * Get all sales for a specific company.
     *
     * @param string $companyName
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $perPage
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSales(string $companyName, string $startDate = null, string $endDate = null, int $perPage = null)
    {
        $query = Order::with(['buyer:id,name,email,company_name,role', 'seller:id,name,email,company_name']);

        // Only filter by company if not admin (companyName = '*' means show all)
        if ($companyName !== '*') {
            $query->whereHas('seller', function($query) use ($companyName) {
                $query->where('company_name', $companyName);
            });
        }

        // Add date filtering if provided
        if ($startDate && $endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Return paginated or all results
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get total sales amount for a specific company.
     *
     * @param string $companyName
     * @return float
     */
    public function getTotalSales(string $companyName): float
    {
        $query = Order::query();

        if ($companyName !== '*') {
            $query->whereHas('seller', function($query) use ($companyName) {
                $query->where('company_name', $companyName);
            });
        }

        return $query->sum('price');
    }

    /**
     * Get sales summary for a specific company.
     *
     * @param string $companyName
     * @return array
     */
    public function getSalesSummary(string $companyName): array
    {
        $query = Order::query();

        if ($companyName !== '*') {
            $query->whereHas('seller', function ($q) use ($companyName) {
                $q->where('company_name', $companyName);
            });
        }

        $summary = $query->select(
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(price) as total_revenue'),
            DB::raw('AVG(price) as average_order_value')
        )->first();

        return $summary ? $summary->toArray() : [
            'order_count' => 0,
            'total_revenue' => 0,
            'average_order_value' => 0,
        ];
    }

    /**
     * Get sales data grouped by day for a chart.
     *
     * @param string $companyName
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSalesGroupedByDay(string $companyName, string $startDate, string $endDate)
    {
        $endDate = Carbon::parse($endDate)->endOfDay();
        $query = Order::query();

        if ($companyName !== '*') {
            $query->whereHas('seller', function ($q) use ($companyName) {
                $q->where('company_name', $companyName);
            });
        }

        return $query->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(price) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }
}
