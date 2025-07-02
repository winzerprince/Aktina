<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemMetric extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'system_uptime' => 'decimal:2',
        'average_response_time' => 'decimal:2',
        'performance_data' => 'array',
    ];

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeHighActivity($query, $threshold = 100)
    {
        return $query->where('active_users', '>', $threshold);
    }

    // Helper methods
    public function getSystemHealth()
    {
        $health = 100;
        
        // Deduct points for low uptime
        if ($this->system_uptime < 99) {
            $health -= (99 - $this->system_uptime) * 2;
        }
        
        // Deduct points for high error rate
        if ($this->api_requests > 0) {
            $errorRate = ($this->error_count / $this->api_requests) * 100;
            if ($errorRate > 1) {
                $health -= $errorRate * 5;
            }
        }
        
        // Deduct points for slow response time
        if ($this->average_response_time > 1000) {
            $health -= min(20, ($this->average_response_time - 1000) / 100);
        }
        
        return max(0, $health);
    }

    public function getOrderCompletionRate()
    {
        if ($this->total_orders > 0) {
            return ($this->completed_orders / $this->total_orders) * 100;
        }
        return 0;
    }

    public function getUserGrowthRate($previousPeriod)
    {
        if ($previousPeriod && $previousPeriod->active_users > 0) {
            return (($this->active_users - $previousPeriod->active_users) / $previousPeriod->active_users) * 100;
        }
        return 0;
    }

    public static function calculateForDate($date)
    {
        $activeUsers = User::whereDate('last_login_at', $date)->count();
        $newUsers = User::whereDate('created_at', $date)->count();
        $totalOrders = Order::whereDate('created_at', $date)->count();
        $completedOrders = Order::whereDate('created_at', $date)->where('status', 'completed')->count();
        $pendingOrders = Order::whereDate('created_at', $date)->where('status', 'pending')->count();
        $cancelledOrders = Order::whereDate('created_at', $date)->where('status', 'cancelled')->count();

        return static::updateOrCreate([
            'date' => $date,
        ], [
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'cancelled_orders' => $cancelledOrders,
            // Additional system metrics would be calculated here
        ]);
    }

    public function getSystemTrends($days = 30)
    {
        return static::where('date', '>=', $this->date->subDays($days))
                    ->orderBy('date')
                    ->get(['date', 'active_users', 'total_orders', 'system_uptime']);
    }
}
