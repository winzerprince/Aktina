<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Retailer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MLDataRepository
{
    /**
     * Get retailer demographics data for ML processing
     *
     * @return Collection
     */
    public function getRetailerDemographicsData(): Collection
    {
        $retailers = Retailer::with(['user'])
            ->select([
                'retailers.id',
                'retailers.male_female_ratio',
                'retailers.city',
                'retailers.urban_rural_classification',
                'retailers.customer_age_class',
                'retailers.customer_income_bracket',
                'retailers.customer_education_level',
                'retailers.business_type',
                'retailers.annual_revenue',
                'retailers.employee_count',
                'retailers.years_in_business',
            ])
            ->get();

        // Enrich with sales data
        $retailerSales = $this->getRetailerTotalSales();

        return $retailers->map(function ($retailer) use ($retailerSales) {
            $data = $retailer->toArray();
            $data['total_sales'] = $retailerSales->get($retailer->id, 0);
            return $data;
        });
    }

    /**
     * Get retailer total sales for categorizing sales volume
     *
     * @return Collection
     */
    private function getRetailerTotalSales(): Collection
    {
        // Get Aktina's user ID
        $aktinaUserId = User::where('name', 'Aktina')->first()?->id;

        if (!$aktinaUserId) {
            return collect();
        }

        // Calculate total sales per retailer from Aktina
        return Order::where('seller_id', $aktinaUserId)
            ->select('buyer_id', DB::raw('SUM(price) as total_sales'))
            ->join('retailers', 'retailers.user_id', '=', 'orders.buyer_id')
            ->groupBy('buyer_id')
            ->pluck('total_sales', 'retailers.id');
    }

    /**
     * Get Aktina sales data for time series forecasting
     *
     * @param int $days Number of past days to fetch
     * @return Collection
     */
    public function getAktinaSalesData(int $days = 180): Collection
    {
        // Get Aktina's user ID
        $aktinaUserId = User::where('name', 'Aktina')->first()?->id;

        if (!$aktinaUserId) {
            return collect();
        }

        $startDate = Carbon::now()->subDays($days);

        return Order::where('seller_id', $aktinaUserId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(price) as amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'amount' => (float) $item->amount,
                ];
            });
    }

    /**
     * Get retailer sales volume categories (high, medium, low)
     *
     * @return Collection
     */
    public function getRetailerSalesVolumeCategories(): Collection
    {
        $salesData = $this->getRetailerTotalSales();

        return $salesData->map(function ($totalSales) {
            if ($totalSales > 10000) {
                return 'high';
            } elseif ($totalSales > 5000) {
                return 'medium';
            } else {
                return 'low';
            }
        });
    }
}
