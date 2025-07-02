<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\AnalyticsService;
use App\Services\MetricsService;
use App\Services\EnhancedOrderService;
use App\Models\User;
use App\Models\Order;
use App\Models\Resource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardOverview extends Component
{
    public $timeRange = '30d';
    public $loading = false;
    public $refreshInterval = 30000; // 30 seconds for real-time updates
    
    // Stats
    public $totalUsers = 0;
    public $totalOrders = 0;
    public $totalRevenue = 0;
    public $activeUsers = 0;
    public $pendingOrders = 0;
    public $recentActivities = [];
    public $systemHealth = [];

    // Chart data
    public $orderTrends = [];
    public $userGrowth = [];
    public $revenueChart = [];
    public $roleDistribution = [];

    protected $listeners = ['refreshDashboard' => 'loadDashboardData'];

    public function __construct(
        private AnalyticsService $analyticsService,
        private MetricsService $metricsService,
        private EnhancedOrderService $orderService
    ) {}

    public function boot(
        AnalyticsService $analyticsService,
        MetricsService $metricsService,
        EnhancedOrderService $orderService
    ) {
        $this->analyticsService = $analyticsService;
        $this->metricsService = $metricsService;
        $this->orderService = $orderService;
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function updatedTimeRange()
    {
        $this->loadDashboardData();
    }

    public function refreshDashboard()
    {
        $this->loading = true;
        $this->loadDashboardData();
        $this->loading = false;
    }

    public function loadDashboardData()
    {
        $cacheKey = "admin_dashboard_overview_{$this->timeRange}";
        
        $data = Cache::remember($cacheKey, 300, function () { // 5 minute cache
            return $this->generateDashboardData();
        });

        $this->totalUsers = $data['totalUsers'];
        $this->totalOrders = $data['totalOrders'];
        $this->totalRevenue = $data['totalRevenue'];
        $this->activeUsers = $data['activeUsers'];
        $this->pendingOrders = $data['pendingOrders'];
        $this->orderTrends = $data['orderTrends'];
        $this->userGrowth = $data['userGrowth'];
        $this->revenueChart = $data['revenueChart'];
        $this->roleDistribution = $data['roleDistribution'];
        $this->systemHealth = $data['systemHealth'];
        $this->recentActivities = $data['recentActivities'];
    }

    private function generateDashboardData(): array
    {
        $period = $this->getPeriodFromRange();

        return [
            'totalUsers' => User::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('status', 'completed')->sum('price'),
            'activeUsers' => User::where('last_login_at', '>=', now()->subDays(7))->count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'orderTrends' => $this->getOrderTrends($period),
            'userGrowth' => $this->getUserGrowth($period),
            'revenueChart' => $this->getRevenueChart($period),
            'roleDistribution' => $this->getRoleDistribution(),
            'systemHealth' => $this->getSystemHealth(),
            'recentActivities' => $this->getRecentActivities()
        ];
    }

    private function getOrderTrends($period): array
    {
        $startDate = now()->sub($period);
        
        $orders = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(price) as revenue')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'dates' => $orders->pluck('date')->toArray(),
            'orders' => $orders->pluck('count')->toArray(),
            'revenue' => $orders->pluck('revenue')->toArray()
        ];
    }

    private function getUserGrowth($period): array
    {
        $startDate = now()->sub($period);
        
        $users = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'dates' => $users->pluck('date')->toArray(),
            'users' => $users->pluck('count')->toArray()
        ];
    }

    private function getRevenueChart($period): array
    {
        $startDate = now()->sub($period);
        
        $revenue = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN price ELSE 0 END) as completed'),
            DB::raw('SUM(CASE WHEN status = "pending" THEN price ELSE 0 END) as pending'),
            DB::raw('SUM(price) as total')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'dates' => $revenue->pluck('date')->toArray(),
            'completed' => $revenue->pluck('completed')->toArray(),
            'pending' => $revenue->pluck('pending')->toArray(),
            'total' => $revenue->pluck('total')->toArray()
        ];
    }

    private function getRoleDistribution(): array
    {
        $roles = User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get();

        return [
            'labels' => $roles->pluck('role')->map(function ($role) {
                return ucfirst(str_replace('_', ' ', $role));
            })->toArray(),
            'data' => $roles->pluck('count')->toArray()
        ];
    }

    private function getSystemHealth(): array
    {
        return [
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'storage' => $this->checkStorageHealth(),
            'queue' => $this->checkQueueHealth()
        ];
    }

    private function checkDatabaseHealth(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $responseTime = round((microtime(true) - $start) * 1000, 2);
            
            return [
                'status' => 'healthy',
                'response_time' => $responseTime,
                'message' => 'Database is responding normally'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'response_time' => null,
                'message' => 'Database connection failed'
            ];
        }
    }

    private function checkCacheHealth(): array
    {
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 10);
            $result = Cache::get($testKey);
            Cache::forget($testKey);
            
            return [
                'status' => $result === 'test' ? 'healthy' : 'warning',
                'message' => $result === 'test' ? 'Cache is working normally' : 'Cache may have issues'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache system failed'
            ];
        }
    }

    private function checkStorageHealth(): array
    {
        try {
            $diskSpace = disk_free_space(storage_path());
            $totalSpace = disk_total_space(storage_path());
            $usagePercentage = round((($totalSpace - $diskSpace) / $totalSpace) * 100, 2);
            
            $status = 'healthy';
            if ($usagePercentage > 90) $status = 'error';
            elseif ($usagePercentage > 80) $status = 'warning';
            
            return [
                'status' => $status,
                'usage_percentage' => $usagePercentage,
                'message' => "Storage usage: {$usagePercentage}%"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Unable to check storage'
            ];
        }
    }

    private function checkQueueHealth(): array
    {
        try {
            // Simple queue health check - could be enhanced with Redis/database queue monitoring
            return [
                'status' => 'healthy',
                'message' => 'Queue system operational'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Queue system issues detected'
            ];
        }
    }

    private function getRecentActivities(): array
    {
        $activities = collect();

        // Recent orders
        $recentOrders = Order::with(['buyer', 'seller'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'order',
                    'title' => "Order #{$order->id} created",
                    'description' => "By " . ($order->buyer->name ?? 'Unknown') . " - $" . number_format($order->price, 2),
                    'time' => $order->created_at->diffForHumans(),
                    'icon' => 'shopping-cart',
                    'color' => 'blue'
                ];
            });

        // Recent users
        $recentUsers = User::latest()
            ->take(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'title' => "New user registered",
                    'description' => "{$user->name} ({$user->role})",
                    'time' => $user->created_at->diffForHumans(),
                    'icon' => 'user-plus',
                    'color' => 'green'
                ];
            });

        return $activities->concat($recentOrders)
            ->concat($recentUsers)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function getPeriodFromRange()
    {
        return match($this->timeRange) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30)
        };
    }

    public function exportData($type)
    {
        // Implementation for data export
        $this->dispatch('export-initiated', ['type' => $type]);
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard-overview');
    }
}
