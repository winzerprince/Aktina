<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesAnalytic extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'revenue' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'top_products' => 'array',
        'sales_by_category' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeHighPerforming($query, $threshold = 1000)
    {
        return $query->where('revenue', '>', $threshold);
    }

    // Helper methods
    public function getGrowthRate($previousPeriod)
    {
        if ($previousPeriod && $previousPeriod->revenue > 0) {
            return (($this->revenue - $previousPeriod->revenue) / $previousPeriod->revenue) * 100;
        }
        return 0;
    }

    public function getCustomerRetentionRate()
    {
        if ($this->customers_count > 0) {
            return ($this->returning_customers / $this->customers_count) * 100;
        }
        return 0;
    }

    public function getConversionRate()
    {
        // This would need additional data about website visits or leads
        // For now, return a placeholder calculation
        return 0;
    }

    public static function calculateForUser($userId, $date)
    {
        $orders = Order::where('seller_id', $userId)
                      ->whereDate('created_at', $date)
                      ->where('status', 'completed')
                      ->get();

        $revenue = $orders->sum('total_price');
        $ordersCount = $orders->count();
        $averageOrderValue = $ordersCount > 0 ? $revenue / $ordersCount : 0;

        return static::updateOrCreate([
            'user_id' => $userId,
            'date' => $date,
        ], [
            'revenue' => $revenue,
            'orders_count' => $ordersCount,
            'average_order_value' => $averageOrderValue,
            // Additional calculations would go here
        ]);
    }
}
