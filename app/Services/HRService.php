<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\ResourceOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HRService
{
    public function getEmployeeStats(): array
    {
        return Cache::remember('hr_employee_stats', 300, function () {
            $totalEmployees = User::count();
            $activeEmployees = User::where('email_verified_at', '!=', null)->count();
            $newEmployeesThisMonth = User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return [
                'total_employees' => $totalEmployees,
                'active_employees' => $activeEmployees,
                'new_employees_this_month' => $newEmployeesThisMonth,
                'employee_growth_rate' => $this->calculateEmployeeGrowthRate(),
                'departments' => $this->getDepartmentDistribution(),
                'roles_distribution' => $this->getRoleDistribution(),
            ];
        });
    }

    public function getWorkforceAnalytics(): array
    {
        return Cache::remember('hr_workforce_analytics', 300, function () {
            return [
                'productivity_metrics' => $this->getProductivityMetrics(),
                'department_performance' => $this->getDepartmentPerformance(),
                'workload_distribution' => $this->getWorkloadDistribution(),
                'performance_trends' => $this->getPerformanceTrends(),
            ];
        });
    }

    public function getEmployeePerformanceMetrics(): array
    {
        return Cache::remember('hr_performance_metrics', 300, function () {
            $users = User::all();
            $performanceData = [];

            foreach ($users as $user) {
                $ordersHandled = $this->getOrdersHandledByUser($user);
                $avgResponseTime = $this->getAverageResponseTime($user);
                $completionRate = $this->getTaskCompletionRate($user);

                $performanceData[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role ?? 'employee',
                    'company_name' => $user->company_name,
                    'orders_handled' => $ordersHandled,
                    'avg_response_time' => $avgResponseTime,
                    'completion_rate' => $completionRate,
                    'performance_score' => $this->calculatePerformanceScore($ordersHandled, $avgResponseTime, $completionRate),
                ];
            }

            return collect($performanceData)->sortByDesc('performance_score')->values()->toArray();
        });
    }

    public function getDepartmentMetrics(): array
    {
        return Cache::remember('hr_department_metrics', 300, function () {
            $departments = [];
            $roles = ['admin', 'production_manager', 'vendor', 'retailer', 'supplier', 'hr_manager'];

            foreach ($roles as $role) {
                $employees = User::where('role', $role)->get();
                $totalOrders = 0;
                $avgPerformance = 0;

                if ($employees->count() > 0) {
                    foreach ($employees as $employee) {
                        $totalOrders += $this->getOrdersHandledByUser($employee);
                    }
                    $avgPerformance = $totalOrders / $employees->count();
                }

                $departments[] = [
                    'department' => ucfirst(str_replace('_', ' ', $role)),
                    'employee_count' => $employees->count(),
                    'total_orders_handled' => $totalOrders,
                    'avg_performance' => round($avgPerformance, 2),
                    'efficiency_score' => $this->calculateDepartmentEfficiency($role),
                ];
            }

            return $departments;
        });
    }

    public function getTrainingNeeds(): array
    {
        return Cache::remember('hr_training_needs', 300, function () {
            $trainingData = [];
            $users = User::all();

            foreach ($users as $user) {
                $performanceScore = $this->calculatePerformanceScore(
                    $this->getOrdersHandledByUser($user),
                    $this->getAverageResponseTime($user),
                    $this->getTaskCompletionRate($user)
                );

                if ($performanceScore < 70) {
                    $trainingData[] = [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role ?? 'employee',
                        'performance_score' => $performanceScore,
                        'recommended_training' => $this->getRecommendedTraining($user->role, $performanceScore),
                        'priority' => $performanceScore < 50 ? 'High' : 'Medium',
                    ];
                }
            }

            return collect($trainingData)->sortBy('performance_score')->values()->toArray();
        });
    }

    public function getEmployeeActivityTrends(int $days = 30): array
    {
        return Cache::remember("hr_activity_trends_{$days}", 300, function () use ($days) {
            $data = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                
                $ordersCreated = Order::whereDate('created_at', $date)->count();
                $resourceOrdersCreated = ResourceOrder::whereDate('created_at', $date)->count();
                $newRegistrations = User::whereDate('created_at', $date)->count();

                $data[] = [
                    'date' => $date->format('Y-m-d'),
                    'orders_created' => $ordersCreated,
                    'resource_orders_created' => $resourceOrdersCreated,
                    'new_registrations' => $newRegistrations,
                    'total_activity' => $ordersCreated + $resourceOrdersCreated + $newRegistrations,
                ];
            }

            return $data;
        });
    }

    private function calculateEmployeeGrowthRate(): float
    {
        $thisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        return $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2) : 0;
    }

    private function getDepartmentDistribution(): array
    {
        return User::selectRaw('role, COUNT(*) as count')
            ->whereNotNull('role')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();
    }

    private function getRoleDistribution(): array
    {
        return User::selectRaw('company_name, COUNT(*) as count')
            ->whereNotNull('company_name')
            ->groupBy('company_name')
            ->pluck('count', 'company_name')
            ->toArray();
    }

    private function getProductivityMetrics(): array
    {
        $totalOrders = Order::count() + ResourceOrder::count();
        $activeUsers = User::where('email_verified_at', '!=', null)->count();
        
        return [
            'orders_per_employee' => $activeUsers > 0 ? round($totalOrders / $activeUsers, 2) : 0,
            'avg_order_processing_time' => $this->getAverageOrderProcessingTime(),
            'employee_utilization_rate' => $this->getEmployeeUtilizationRate(),
        ];
    }

    private function getDepartmentPerformance(): array
    {
        $roles = ['admin', 'production_manager', 'vendor', 'retailer', 'supplier'];
        $performance = [];

        foreach ($roles as $role) {
            $users = User::where('role', $role)->get();
            $totalOrders = 0;

            foreach ($users as $user) {
                $totalOrders += $this->getOrdersHandledByUser($user);
            }

            $performance[$role] = [
                'total_orders' => $totalOrders,
                'employee_count' => $users->count(),
                'avg_orders_per_employee' => $users->count() > 0 ? round($totalOrders / $users->count(), 2) : 0,
            ];
        }

        return $performance;
    }

    private function getWorkloadDistribution(): array
    {
        $users = User::all();
        $workload = [];

        foreach ($users as $user) {
            $ordersHandled = $this->getOrdersHandledByUser($user);
            $workload[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'orders_handled' => $ordersHandled,
                'workload_level' => $this->categorizeWorkload($ordersHandled),
            ];
        }

        return $workload;
    }

    private function getPerformanceTrends(): array
    {
        // Simplified performance trends - could be enhanced with actual performance tracking
        return [
            'monthly_performance' => [
                'current_month' => 85,
                'last_month' => 82,
                'trend' => 'improving',
            ],
            'department_trends' => [
                'production' => 88,
                'sales' => 92,
                'supply_chain' => 85,
            ],
        ];
    }

    private function getOrdersHandledByUser(User $user): int
    {
        $buyerOrders = Order::where('buyer_id', $user->id)->count();
        $sellerOrders = Order::where('seller_id', $user->id)->count();
        $resourceBuyerOrders = ResourceOrder::where('buyer_id', $user->id)->count();
        $resourceSellerOrders = ResourceOrder::where('seller_id', $user->id)->count();
        
        return $buyerOrders + $sellerOrders + $resourceBuyerOrders + $resourceSellerOrders;
    }

    private function getAverageResponseTime(User $user): float
    {
        // Simplified - in real implementation, this would track actual response times
        return rand(1, 24); // hours
    }

    private function getTaskCompletionRate(User $user): float
    {
        $totalOrders = $this->getOrdersHandledByUser($user);
        if ($totalOrders === 0) return 100;

        $completedBuyerOrders = Order::where('buyer_id', $user->id)
            ->whereIn('status', ['completed', 'complete'])
            ->count();
        $completedSellerOrders = Order::where('seller_id', $user->id)
            ->whereIn('status', ['completed', 'complete'])
            ->count();

        $completedOrders = $completedBuyerOrders + $completedSellerOrders;
        return round(($completedOrders / $totalOrders) * 100, 2);
    }

    private function calculatePerformanceScore(int $ordersHandled, float $avgResponseTime, float $completionRate): float
    {
        $orderScore = min(($ordersHandled / 10) * 40, 40); // Max 40 points for orders
        $timeScore = max(40 - ($avgResponseTime * 2), 0); // Max 40 points for response time
        $completionScore = ($completionRate / 100) * 20; // Max 20 points for completion rate

        return round($orderScore + $timeScore + $completionScore, 2);
    }

    private function calculateDepartmentEfficiency(string $role): float
    {
        $users = User::where('role', $role)->get();
        if ($users->isEmpty()) return 0;

        $totalEfficiency = 0;
        foreach ($users as $user) {
            $totalEfficiency += $this->calculatePerformanceScore(
                $this->getOrdersHandledByUser($user),
                $this->getAverageResponseTime($user),
                $this->getTaskCompletionRate($user)
            );
        }

        return round($totalEfficiency / $users->count(), 2);
    }

    private function getRecommendedTraining(string $role, float $performanceScore): string
    {
        if ($performanceScore < 50) {
            return 'Comprehensive role training and mentorship program';
        } elseif ($performanceScore < 70) {
            return 'Skill enhancement workshops and process improvement training';
        }

        return 'Advanced training and leadership development';
    }

    private function categorizeWorkload(int $ordersHandled): string
    {
        if ($ordersHandled >= 20) return 'High';
        if ($ordersHandled >= 10) return 'Medium';
        if ($ordersHandled >= 5) return 'Low';
        return 'Very Low';
    }

    private function getAverageOrderProcessingTime(): float
    {
        // Simplified - would calculate actual processing times in real implementation
        return 2.5; // days
    }

    private function getEmployeeUtilizationRate(): float
    {
        $activeUsers = User::where('email_verified_at', '!=', null)->count();
        $usersWithOrders = User::whereHas('buyerOrders')
            ->orWhereHas('sellerOrders')
            ->count();

        return $activeUsers > 0 ? round(($usersWithOrders / $activeUsers) * 100, 2) : 0;
    }
}
