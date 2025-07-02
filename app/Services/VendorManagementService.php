<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorManagementService
{
    public function getVendors(array $filters = [], int $perPage = 20)
    {
        $query = User::where('role', 'vendor')
                    ->with(['orders' => function($q) {
                        $q->select('id', 'created_by', 'total_amount', 'status', 'created_at');
                    }]);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function getSuppliers(array $filters = [], int $perPage = 20)
    {
        $query = User::where('role', 'supplier')
                    ->with(['orders' => function($q) {
                        $q->select('id', 'created_by', 'total_amount', 'status', 'created_at');
                    }]);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('company_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            } elseif ($filters['status'] === 'suspended') {
                $query->where('status', 'suspended');
            }
        }

        // Performance filtering would be implemented based on calculated metrics
        if (!empty($filters['performance'])) {
            $this->applyPerformanceFilter($query, $filters['performance']);
        }
    }

    private function applyPerformanceFilter($query, $performance)
    {
        // This would join with a calculated performance table or use raw SQL
        // For now, we'll use a simple approximation based on order completion rates
        $subQuery = Order::select('created_by')
            ->selectRaw('(COUNT(CASE WHEN status = "completed" THEN 1 END) * 100.0 / COUNT(*)) as completion_rate')
            ->groupBy('created_by');

        switch ($performance) {
            case 'excellent':
                $query->whereIn('id', $subQuery->having('completion_rate', '>=', 90)->pluck('created_by'));
                break;
            case 'good':
                $query->whereIn('id', $subQuery->havingRaw('completion_rate >= 70 AND completion_rate < 90')->pluck('created_by'));
                break;
            case 'average':
                $query->whereIn('id', $subQuery->havingRaw('completion_rate >= 50 AND completion_rate < 70')->pluck('created_by'));
                break;
            case 'poor':
                $query->whereIn('id', $subQuery->having('completion_rate', '<', 50)->pluck('created_by'));
                break;
        }
    }

    public function getVendorStatistics()
    {
        $totalVendors = User::where('role', 'vendor')->count();
        $activeVendors = User::where('role', 'vendor')->where('is_active', true)->count();
        
        return [
            'total' => $totalVendors,
            'active' => $activeVendors,
            'inactive' => $totalVendors - $activeVendors,
            'new_this_month' => User::where('role', 'vendor')
                                   ->whereMonth('created_at', Carbon::now()->month)
                                   ->count(),
            'avg_performance' => $this->calculateAveragePerformance('vendor'),
            'total_orders' => $this->getTotalOrdersByRole('vendor'),
            'revenue_generated' => $this->getRevenueByRole('vendor'),
            'top_performers' => $this->getTopPerformers('vendor', 5),
        ];
    }

    public function getSupplierStatistics()
    {
        $totalSuppliers = User::where('role', 'supplier')->count();
        $activeSuppliers = User::where('role', 'supplier')->where('is_active', true)->count();
        
        return [
            'total' => $totalSuppliers,
            'active' => $activeSuppliers,
            'inactive' => $totalSuppliers - $activeSuppliers,
            'new_this_month' => User::where('role', 'supplier')
                                   ->whereMonth('created_at', Carbon::now()->month)
                                   ->count(),
            'avg_performance' => $this->calculateAveragePerformance('supplier'),
            'total_orders' => $this->getTotalOrdersByRole('supplier'),
            'cost_savings' => $this->getCostSavingsByRole('supplier'),
            'top_performers' => $this->getTopPerformers('supplier', 5),
        ];
    }

    public function getVendorDetails(int $vendorId)
    {
        $vendor = User::with(['orders.items', 'orders.status'])
                     ->findOrFail($vendorId);

        $orderStats = $this->getVendorOrderStatistics($vendorId);
        $performanceMetrics = $this->getVendorPerformanceMetrics($vendorId);
        $recentOrders = $vendor->orders()->latest()->take(10)->get();
        $monthlyRevenue = $this->getMonthlyRevenue($vendorId);

        return [
            'vendor' => $vendor,
            'order_stats' => $orderStats,
            'performance_metrics' => $performanceMetrics,
            'recent_orders' => $recentOrders,
            'monthly_revenue' => $monthlyRevenue,
            'ratings' => $this->getVendorRatings($vendorId),
            'communication_history' => $this->getCommunicationHistory($vendorId),
        ];
    }

    private function getVendorOrderStatistics(int $vendorId)
    {
        $orders = Order::where('created_by', $vendorId);

        return [
            'total_orders' => $orders->count(),
            'completed_orders' => $orders->where('status', 'completed')->count(),
            'pending_orders' => $orders->where('status', 'pending')->count(),
            'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
            'total_value' => $orders->sum('total_amount'),
            'avg_order_value' => $orders->avg('total_amount'),
        ];
    }

    private function getVendorPerformanceMetrics(int $vendorId)
    {
        $orders = Order::where('created_by', $vendorId);
        $totalOrders = $orders->count();

        if ($totalOrders === 0) {
            return [
                'completion_rate' => 0,
                'on_time_delivery' => 0,
                'quality_score' => 0,
                'response_time' => 0,
                'overall_rating' => 0,
            ];
        }

        $completedOrders = $orders->where('status', 'completed')->count();
        $onTimeDeliveries = $orders->where('status', 'completed')
                                  ->where('delivered_at', '<=', DB::raw('expected_delivery_date'))
                                  ->count();

        return [
            'completion_rate' => round(($completedOrders / $totalOrders) * 100, 1),
            'on_time_delivery' => $completedOrders > 0 ? round(($onTimeDeliveries / $completedOrders) * 100, 1) : 0,
            'quality_score' => $this->getQualityScore($vendorId),
            'response_time' => $this->getAverageResponseTime($vendorId),
            'overall_rating' => $this->getOverallRating($vendorId),
        ];
    }

    private function calculateAveragePerformance(string $role)
    {
        $users = User::where('role', $role)->get();
        
        if ($users->isEmpty()) return 0;

        $totalRating = $users->sum(function ($user) {
            return $this->getOverallRating($user->id);
        });

        return round($totalRating / $users->count(), 1);
    }

    private function getTotalOrdersByRole(string $role)
    {
        return Order::whereHas('creator', function ($query) use ($role) {
            $query->where('role', $role);
        })->count();
    }

    private function getRevenueByRole(string $role)
    {
        return Order::whereHas('creator', function ($query) use ($role) {
            $query->where('role', $role);
        })->where('status', 'completed')->sum('total_amount');
    }

    private function getCostSavingsByRole(string $role)
    {
        // Simplified calculation - in practice, this would be more complex
        return Order::whereHas('creator', function ($query) use ($role) {
            $query->where('role', $role);
        })->where('status', 'completed')->sum('discount_amount');
    }

    private function getTopPerformers(string $role, int $limit)
    {
        return User::where('role', $role)
                  ->where('is_active', true)
                  ->get()
                  ->map(function ($user) {
                      $user->performance_rating = $this->getOverallRating($user->id);
                      return $user;
                  })
                  ->sortByDesc('performance_rating')
                  ->take($limit)
                  ->values();
    }

    private function getQualityScore(int $userId)
    {
        // This would be based on reviews, returns, etc.
        // For now, return a random score between 70-95
        return mt_rand(70, 95);
    }

    private function getAverageResponseTime(int $userId)
    {
        // This would be calculated from communication logs
        // For now, return a value between 1-24 hours
        return mt_rand(1, 24) . ' hours';
    }

    private function getOverallRating(int $userId)
    {
        $metrics = $this->getVendorPerformanceMetrics($userId);
        
        // Calculate weighted average
        $weights = [
            'completion_rate' => 0.3,
            'on_time_delivery' => 0.3,
            'quality_score' => 0.25,
            'response_time' => 0.15,
        ];

        $totalScore = 0;
        foreach ($weights as $metric => $weight) {
            if ($metric === 'response_time') {
                // Convert response time to score (lower is better)
                $hours = (int) $metrics[$metric];
                $score = max(0, 100 - ($hours * 4)); // Subtract 4 points per hour
                $totalScore += $score * $weight;
            } else {
                $totalScore += $metrics[$metric] * $weight;
            }
        }

        return round($totalScore, 1);
    }

    private function getMonthlyRevenue(int $vendorId)
    {
        $months = collect();
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Order::where('created_by', $vendorId)
                           ->where('status', 'completed')
                           ->whereYear('created_at', $date->year)
                           ->whereMonth('created_at', $date->month)
                           ->sum('total_amount');
            
            $months->push([
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ]);
        }

        return $months;
    }

    private function getVendorRatings(int $vendorId)
    {
        // This would come from a ratings table
        // For now, return sample data
        return [
            'average_rating' => mt_rand(35, 50) / 10, // 3.5 to 5.0
            'total_reviews' => mt_rand(10, 100),
            'five_star' => mt_rand(40, 70),
            'four_star' => mt_rand(20, 30),
            'three_star' => mt_rand(5, 15),
            'two_star' => mt_rand(0, 5),
            'one_star' => mt_rand(0, 3),
        ];
    }

    private function getCommunicationHistory(int $vendorId)
    {
        // This would come from messages table
        // For now, return empty array
        return [];
    }

    public function updateVendorStatus(int $vendorId, string $status)
    {
        $vendor = User::findOrFail($vendorId);
        
        $updateData = [];
        
        if (in_array($status, ['active', 'inactive'])) {
            $updateData['is_active'] = $status === 'active';
        } elseif ($status === 'suspended') {
            $updateData['status'] = 'suspended';
            $updateData['is_active'] = false;
        }

        $vendor->update($updateData);

        // Log the action
        logger()->info("Vendor status updated", [
            'vendor_id' => $vendorId,
            'new_status' => $status,
            'updated_by' => auth()->id()
        ]);

        return $vendor;
    }

    public function exportVendors(array $filters = [])
    {
        // In a real implementation, this would generate CSV/Excel export
        logger()->info("Vendor export requested", [
            'filters' => $filters,
            'requested_by' => auth()->id()
        ]);

        return true;
    }
}
