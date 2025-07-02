<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LiveActivityService
{
    public function getRecentActivities(int $limit = 50, array $types = ['all']): array
    {
        return Cache::remember("recent_activities_" . implode('_', $types) . "_{$limit}", 30, function () use ($limit, $types) {
            $activities = [];
            
            // User activities
            if (in_array('all', $types) || in_array('users', $types)) {
                $userActivities = $this->getUserActivities($limit);
                $activities = array_merge($activities, $userActivities);
            }
            
            // Order activities
            if (in_array('all', $types) || in_array('orders', $types)) {
                $orderActivities = $this->getOrderActivities($limit);
                $activities = array_merge($activities, $orderActivities);
            }
            
            // System activities
            if (in_array('all', $types) || in_array('system', $types)) {
                $systemActivities = $this->getSystemActivities($limit);
                $activities = array_merge($activities, $systemActivities);
            }
            
            // Product activities
            if (in_array('all', $types) || in_array('products', $types)) {
                $productActivities = $this->getProductActivities($limit);
                $activities = array_merge($activities, $productActivities);
            }
            
            // Sort by timestamp and limit
            usort($activities, function ($a, $b) {
                return Carbon::parse($b['timestamp'])->timestamp - Carbon::parse($a['timestamp'])->timestamp;
            });
            
            return array_slice($activities, 0, $limit);
        });
    }

    public function getActivityStats(): array
    {
        return Cache::remember('activity_stats', 60, function () {
            return [
                'total_today' => $this->getTodayActivityCount(),
                'active_users' => $this->getActiveUsersCount(),
                'peak_hour' => $this->getPeakActivityHour(),
                'activity_breakdown' => [
                    'users' => $this->getActivityCountByType('users'),
                    'orders' => $this->getActivityCountByType('orders'),
                    'system' => $this->getActivityCountByType('system'),
                    'products' => $this->getActivityCountByType('products')
                ]
            ];
        });
    }

    public function getActiveSessions(): array
    {
        return Cache::remember('active_sessions', 30, function () {
            // Simulate active session data
            $sessions = [];
            
            for ($i = 0; $i < rand(5, 25); $i++) {
                $sessions[] = [
                    'id' => 'sess_' . uniqid(),
                    'user_id' => rand(1, 100),
                    'user_name' => 'User ' . rand(1, 100),
                    'user_role' => ['admin', 'vendor', 'supplier', 'retailer'][rand(0, 3)],
                    'ip_address' => rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255),
                    'location' => ['New York', 'London', 'Tokyo', 'Sydney', 'Berlin'][rand(0, 4)],
                    'device' => ['Desktop', 'Mobile', 'Tablet'][rand(0, 2)],
                    'browser' => ['Chrome', 'Firefox', 'Safari', 'Edge'][rand(0, 3)],
                    'started_at' => now()->subMinutes(rand(1, 120))->toISOString(),
                    'last_activity' => now()->subMinutes(rand(0, 30))->toISOString(),
                    'pages_viewed' => rand(1, 20),
                    'status' => ['active', 'idle'][rand(0, 1)]
                ];
            }
            
            return $sessions;
        });
    }

    public function getSystemEvents(): array
    {
        return Cache::remember('system_events', 60, function () {
            $events = [];
            
            // Generate system events
            $eventTypes = [
                ['type' => 'backup_completed', 'severity' => 'info', 'message' => 'Database backup completed successfully'],
                ['type' => 'cache_cleared', 'severity' => 'info', 'message' => 'Application cache cleared'],
                ['type' => 'high_memory_usage', 'severity' => 'warning', 'message' => 'Memory usage exceeded 80%'],
                ['type' => 'slow_query_detected', 'severity' => 'warning', 'message' => 'Slow query detected on orders table'],
                ['type' => 'failed_login_attempt', 'severity' => 'security', 'message' => 'Multiple failed login attempts detected'],
                ['type' => 'system_maintenance', 'severity' => 'info', 'message' => 'Scheduled maintenance window started']
            ];
            
            for ($i = 0; $i < rand(10, 30); $i++) {
                $event = $eventTypes[rand(0, count($eventTypes) - 1)];
                $events[] = [
                    'id' => 'evt_' . uniqid(),
                    'type' => $event['type'],
                    'severity' => $event['severity'],
                    'message' => $event['message'],
                    'timestamp' => now()->subMinutes(rand(0, 240))->toISOString(),
                    'source' => 'system',
                    'acknowledged' => rand(0, 1) == 1
                ];
            }
            
            return $events;
        });
    }

    public function exportActivities(array $types): array
    {
        return [
            'activities' => $this->getRecentActivities(1000, $types),
            'stats' => $this->getActivityStats(),
            'sessions' => $this->getActiveSessions(),
            'events' => $this->getSystemEvents(),
            'exported_at' => now()->toISOString()
        ];
    }

    public function clearActivities(): bool
    {
        Cache::forget('recent_activities_all_50');
        Cache::forget('activity_stats');
        return true;
    }

    public function pauseActivity(string $activityId): bool
    {
        // Implementation would depend on your activity tracking system
        return true;
    }

    private function getUserActivities(int $limit): array
    {
        $activities = [];
        
        // Recent user registrations
        $recentUsers = User::orderBy('created_at', 'desc')->limit($limit / 4)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'id' => 'user_reg_' . $user->id,
                'type' => 'user_registration',
                'category' => 'users',
                'title' => 'New User Registration',
                'description' => "User {$user->name} registered",
                'user' => ['id' => $user->id, 'name' => $user->name, 'role' => $user->role],
                'icon' => 'user-plus',
                'color' => 'blue',
                'timestamp' => $user->created_at->toISOString(),
                'data' => ['user_id' => $user->id, 'email' => $user->email]
            ];
        }
        
        // Recent logins (simulated)
        for ($i = 0; $i < rand(5, 15); $i++) {
            $user = User::inRandomOrder()->first();
            if ($user) {
                $activities[] = [
                    'id' => 'user_login_' . uniqid(),
                    'type' => 'user_login',
                    'category' => 'users',
                    'title' => 'User Login',
                    'description' => "User {$user->name} logged in",
                    'user' => ['id' => $user->id, 'name' => $user->name, 'role' => $user->role],
                    'icon' => 'login',
                    'color' => 'green',
                    'timestamp' => now()->subMinutes(rand(0, 60))->toISOString(),
                    'data' => ['ip' => rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255)]
                ];
            }
        }
        
        return $activities;
    }

    private function getOrderActivities(int $limit): array
    {
        $activities = [];
        
        // Recent orders
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->limit($limit / 4)->get();
        foreach ($recentOrders as $order) {
            $activities[] = [
                'id' => 'order_created_' . $order->id,
                'type' => 'order_created',
                'category' => 'orders',
                'title' => 'New Order',
                'description' => "Order #{$order->id} created by {$order->user->name}",
                'user' => ['id' => $order->user->id, 'name' => $order->user->name, 'role' => $order->user->role],
                'icon' => 'shopping-cart',
                'color' => 'green',
                'timestamp' => $order->created_at->toISOString(),
                'data' => ['order_id' => $order->id, 'amount' => $order->total_amount, 'status' => $order->status]
            ];
        }
        
        // Order status changes (simulated)
        $statusChanges = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        for ($i = 0; $i < rand(3, 10); $i++) {
            $order = Order::with('user')->inRandomOrder()->first();
            if ($order) {
                $newStatus = $statusChanges[rand(0, count($statusChanges) - 1)];
                $activities[] = [
                    'id' => 'order_status_' . $order->id . '_' . uniqid(),
                    'type' => 'order_status_change',
                    'category' => 'orders',
                    'title' => 'Order Status Updated',
                    'description' => "Order #{$order->id} status changed to {$newStatus}",
                    'user' => ['id' => $order->user->id, 'name' => $order->user->name, 'role' => $order->user->role],
                    'icon' => 'refresh',
                    'color' => 'yellow',
                    'timestamp' => now()->subMinutes(rand(0, 120))->toISOString(),
                    'data' => ['order_id' => $order->id, 'new_status' => $newStatus, 'old_status' => $order->status]
                ];
            }
        }
        
        return $activities;
    }

    private function getSystemActivities(int $limit): array
    {
        $activities = [];
        
        $systemEvents = [
            ['type' => 'backup', 'title' => 'System Backup', 'description' => 'Automated system backup completed', 'color' => 'blue'],
            ['type' => 'maintenance', 'title' => 'Maintenance', 'description' => 'Scheduled maintenance completed', 'color' => 'gray'],
            ['type' => 'security', 'title' => 'Security Alert', 'description' => 'Suspicious activity detected', 'color' => 'red'],
            ['type' => 'performance', 'title' => 'Performance Alert', 'description' => 'High CPU usage detected', 'color' => 'orange']
        ];
        
        for ($i = 0; $i < rand(5, 15); $i++) {
            $event = $systemEvents[rand(0, count($systemEvents) - 1)];
            $activities[] = [
                'id' => 'system_' . $event['type'] . '_' . uniqid(),
                'type' => 'system_' . $event['type'],
                'category' => 'system',
                'title' => $event['title'],
                'description' => $event['description'],
                'user' => null,
                'icon' => 'server',
                'color' => $event['color'],
                'timestamp' => now()->subMinutes(rand(0, 180))->toISOString(),
                'data' => ['event_type' => $event['type'], 'automated' => true]
            ];
        }
        
        return $activities;
    }

    private function getProductActivities(int $limit): array
    {
        $activities = [];
        
        // Recent product updates (simulated)
        for ($i = 0; $i < rand(3, 8); $i++) {
            $product = Product::inRandomOrder()->first();
            if ($product) {
                $activities[] = [
                    'id' => 'product_updated_' . $product->id . '_' . uniqid(),
                    'type' => 'product_updated',
                    'category' => 'products',
                    'title' => 'Product Updated',
                    'description' => "Product '{$product->name}' inventory updated",
                    'user' => null,
                    'icon' => 'package',
                    'color' => 'purple',
                    'timestamp' => now()->subMinutes(rand(0, 240))->toISOString(),
                    'data' => ['product_id' => $product->id, 'stock_quantity' => $product->stock_quantity]
                ];
            }
        }
        
        return $activities;
    }

    private function getTodayActivityCount(): int
    {
        return rand(150, 500);
    }

    private function getActiveUsersCount(): int
    {
        return rand(25, 100);
    }

    private function getPeakActivityHour(): string
    {
        $hours = ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'];
        return $hours[rand(0, count($hours) - 1)];
    }

    private function getActivityCountByType(string $type): int
    {
        return match ($type) {
            'users' => rand(20, 80),
            'orders' => rand(30, 120),
            'system' => rand(10, 40),
            'products' => rand(15, 60),
            default => 0
        };
    }
}
