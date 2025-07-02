<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class UserActivityTracking extends Component
{
    use WithPagination;

    public $activeUsers = [];
    public $recentSessions = [];
    public $activityStats = [];
    public $selectedTimeframe = '24h';
    public $selectedRole = 'all';
    public $searchTerm = '';
    public $showDetails = false;
    public $selectedUserId = null;
    public $userDetails = [];

    public $timeframes = [
        '1h' => 'Last Hour',
        '24h' => 'Last 24 Hours',
        '7d' => 'Last 7 Days',
        '30d' => 'Last 30 Days'
    ];

    public $roles = [
        'all' => 'All Roles',
        'admin' => 'Admin',
        'production_manager' => 'Production Manager',
        'supplier' => 'Supplier',
        'vendor' => 'Vendor',
        'retailer' => 'Retailer'
    ];

    public function mount()
    {
        $this->loadActivityData();
    }

    public function updatedSelectedTimeframe()
    {
        $this->loadActivityData();
        $this->resetPage();
    }

    public function updatedSelectedRole()
    {
        $this->loadActivityData();
        $this->resetPage();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function loadActivityData()
    {
        $this->loadActiveUsers();
        $this->loadRecentSessions();
        $this->loadActivityStats();
    }

    private function loadActiveUsers()
    {
        $timeframe = $this->getTimeframeDate();
        
        $query = User::select('users.*', 'sessions.last_activity', 'sessions.ip_address')
            ->leftJoin('sessions', 'users.id', '=', 'sessions.user_id')
            ->where('sessions.last_activity', '>=', $timeframe)
            ->where('users.is_active', true);

        if ($this->selectedRole !== 'all') {
            $query->where('users.role', $this->selectedRole);
        }

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('users.name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('users.email', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $this->activeUsers = $query->orderBy('sessions.last_activity', 'desc')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'last_activity' => $user->last_activity ? \Carbon\Carbon::parse($user->last_activity)->diffForHumans() : 'Never',
                    'ip_address' => $user->ip_address,
                    'status' => $this->getUserStatus($user->last_activity),
                ];
            })
            ->toArray();
    }

    private function loadRecentSessions()
    {
        $timeframe = $this->getTimeframeDate();
        
        $sessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('users.name', 'users.email', 'users.role', 'sessions.*')
            ->where('sessions.last_activity', '>=', $timeframe->timestamp)
            ->orderBy('sessions.last_activity', 'desc')
            ->take(20)
            ->get();

        $this->recentSessions = $sessions->map(function ($session) {
            return [
                'user_name' => $session->name,
                'user_email' => $session->email,
                'role' => $session->role,
                'ip_address' => $session->ip_address,
                'user_agent' => $this->parseUserAgent($session->user_agent),
                'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'session_duration' => $this->calculateSessionDuration($session),
            ];
        })->toArray();
    }

    private function loadActivityStats()
    {
        $timeframe = $this->getTimeframeDate();
        
        // Active users count
        $activeUsersCount = User::join('sessions', 'users.id', '=', 'sessions.user_id')
            ->where('sessions.last_activity', '>=', $timeframe->timestamp)
            ->distinct()
            ->count('users.id');

        // Total sessions
        $totalSessions = DB::table('sessions')
            ->where('last_activity', '>=', $timeframe->timestamp)
            ->count();

        // Orders created in timeframe
        $ordersCount = Order::where('created_at', '>=', $timeframe)->count();

        // User registrations in timeframe
        $newUsers = User::where('created_at', '>=', $timeframe)->count();

        // Role distribution
        $roleDistribution = User::join('sessions', 'users.id', '=', 'sessions.user_id')
            ->where('sessions.last_activity', '>=', $timeframe->timestamp)
            ->select('users.role', DB::raw('count(distinct users.id) as count'))
            ->groupBy('users.role')
            ->get()
            ->pluck('count', 'role')
            ->toArray();

        $this->activityStats = [
            'active_users' => $activeUsersCount,
            'total_sessions' => $totalSessions,
            'orders_created' => $ordersCount,
            'new_registrations' => $newUsers,
            'role_distribution' => $roleDistribution,
            'avg_session_time' => $this->calculateAverageSessionTime($timeframe),
        ];
    }

    private function getTimeframeDate()
    {
        return match($this->selectedTimeframe) {
            '1h' => now()->subHour(),
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subDay(),
        };
    }

    private function getUserStatus($lastActivity)
    {
        if (!$lastActivity) return 'offline';
        
        $lastActivityTime = \Carbon\Carbon::parse($lastActivity);
        $minutesAgo = now()->diffInMinutes($lastActivityTime);
        
        if ($minutesAgo <= 5) return 'online';
        if ($minutesAgo <= 30) return 'away';
        return 'offline';
    }

    private function parseUserAgent($userAgent)
    {
        // Basic user agent parsing
        if (str_contains($userAgent, 'Chrome')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Safari';
        if (str_contains($userAgent, 'Edge')) return 'Edge';
        return 'Unknown';
    }

    private function calculateSessionDuration($session)
    {
        // This is simplified - in a real app you'd track session start times
        $lastActivity = \Carbon\Carbon::createFromTimestamp($session->last_activity);
        $now = now();
        
        if ($now->diffInMinutes($lastActivity) < 30) {
            return $now->diffInMinutes($lastActivity) . ' min';
        }
        
        return $now->diffInHours($lastActivity) . ' hours';
    }

    private function calculateAverageSessionTime($timeframe)
    {
        // Simplified calculation - in production you'd have proper session tracking
        $sessions = DB::table('sessions')
            ->where('last_activity', '>=', $timeframe->timestamp)
            ->get();
            
        if ($sessions->isEmpty()) return '0 min';
        
        $totalMinutes = $sessions->sum(function ($session) {
            return now()->diffInMinutes(\Carbon\Carbon::createFromTimestamp($session->last_activity));
        });
        
        $avgMinutes = $totalMinutes / $sessions->count();
        
        if ($avgMinutes < 60) {
            return round($avgMinutes) . ' min';
        }
        
        return round($avgMinutes / 60, 1) . ' hours';
    }

    public function showUserDetails($userId)
    {
        $this->selectedUserId = $userId;
        $this->loadUserDetails($userId);
        $this->showDetails = true;
    }

    private function loadUserDetails($userId)
    {
        $user = User::find($userId);
        
        if (!$user) return;
        
        $timeframe = $this->getTimeframeDate();
        
        // User's recent orders
        $recentOrders = Order::where('created_by', $userId)
            ->where('created_at', '>=', $timeframe)
            ->with(['items', 'status'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // User's session history
        $sessionHistory = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>=', $timeframe->timestamp)
            ->orderBy('last_activity', 'desc')
            ->get();

        $this->userDetails = [
            'user' => $user,
            'recent_orders' => $recentOrders,
            'session_history' => $sessionHistory,
            'total_orders' => Order::where('created_by', $userId)->count(),
            'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
        ];
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedUserId = null;
        $this->userDetails = [];
    }

    #[On('refresh-activity')]
    public function refreshActivity()
    {
        $this->loadActivityData();
    }

    public function exportActivity()
    {
        // Export functionality would be implemented here
        $this->dispatch('export-started', ['type' => 'activity', 'timeframe' => $this->selectedTimeframe]);
    }

    public function render()
    {
        return view('livewire.admin.user-activity-tracking');
    }
}
