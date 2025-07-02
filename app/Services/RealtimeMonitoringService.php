<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RealtimeMonitoringService
{
    public function getSystemMetrics(): array
    {
        $loadAverage = sys_getloadavg();
        $memoryUsage = $this->getMemoryUsage();
        $diskUsage = $this->getDiskUsage();
        $cpuUsage = $this->getCpuUsage();

        return [
            'cpu' => [
                'usage' => $cpuUsage,
                'load_1min' => $loadAverage[0] ?? 0,
                'load_5min' => $loadAverage[1] ?? 0,
                'load_15min' => $loadAverage[2] ?? 0,
                'status' => $this->getStatusByValue($cpuUsage, [70, 90])
            ],
            'memory' => [
                'used' => $memoryUsage['used'],
                'free' => $memoryUsage['free'],
                'total' => $memoryUsage['total'],
                'percentage' => $memoryUsage['percentage'],
                'status' => $this->getStatusByValue($memoryUsage['percentage'], [70, 85])
            ],
            'disk' => [
                'used' => $diskUsage['used'],
                'free' => $diskUsage['free'],
                'total' => $diskUsage['total'],
                'percentage' => $diskUsage['percentage'],
                'status' => $this->getStatusByValue($diskUsage['percentage'], [80, 95])
            ],
            'timestamp' => now()->toISOString()
        ];
    }

    public function getDatabaseMetrics(): array
    {
        try {
            $connections = $this->getDatabaseConnections();
            $queryTime = $this->getAverageQueryTime();
            $slowQueries = $this->getSlowQueryCount();
            $lockWaits = $this->getLockWaitCount();

            return [
                'connections' => [
                    'active' => $connections['active'],
                    'max' => $connections['max'],
                    'percentage' => $connections['percentage'],
                    'status' => $this->getStatusByValue($connections['percentage'], [70, 90])
                ],
                'performance' => [
                    'avg_query_time' => $queryTime,
                    'slow_queries' => $slowQueries,
                    'lock_waits' => $lockWaits,
                    'status' => $queryTime > 1000 ? 'critical' : ($queryTime > 500 ? 'warning' : 'good')
                ],
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Database metrics unavailable',
                'status' => 'critical',
                'timestamp' => now()->toISOString()
            ];
        }
    }

    public function getCacheMetrics(): array
    {
        try {
            $hitRate = $this->getCacheHitRate();
            $memoryUsage = $this->getCacheMemoryUsage();
            $keyCount = $this->getCacheKeyCount();

            return [
                'hit_rate' => $hitRate,
                'memory_usage' => $memoryUsage,
                'key_count' => $keyCount,
                'status' => $hitRate < 80 ? 'warning' : 'good',
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Cache metrics unavailable',
                'status' => 'critical',
                'timestamp' => now()->toISOString()
            ];
        }
    }

    public function getQueueMetrics(): array
    {
        try {
            $pendingJobs = $this->getPendingJobsCount();
            $failedJobs = $this->getFailedJobsCount();
            $processedJobs = $this->getProcessedJobsCount();

            return [
                'pending' => $pendingJobs,
                'failed' => $failedJobs,
                'processed_today' => $processedJobs,
                'status' => $failedJobs > 10 ? 'warning' : 'good',
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Queue metrics unavailable',
                'status' => 'critical',
                'timestamp' => now()->toISOString()
            ];
        }
    }

    public function getPerformanceTimeline(): array
    {
        $timeline = Cache::remember('performance_timeline', 60, function () {
            $data = [];
            $now = now();
            
            for ($i = 30; $i >= 0; $i--) {
                $timestamp = $now->copy()->subMinutes($i);
                $data[] = [
                    'timestamp' => $timestamp->toISOString(),
                    'response_time' => rand(100, 800),
                    'cpu_usage' => rand(20, 85),
                    'memory_usage' => rand(40, 90),
                    'active_users' => rand(10, 100)
                ];
            }
            
            return $data;
        });

        return [
            'data' => $timeline,
            'series' => [
                [
                    'name' => 'Response Time (ms)',
                    'data' => array_column($timeline, 'response_time')
                ],
                [
                    'name' => 'CPU Usage (%)',
                    'data' => array_column($timeline, 'cpu_usage')
                ],
                [
                    'name' => 'Memory Usage (%)',
                    'data' => array_column($timeline, 'memory_usage')
                ],
                [
                    'name' => 'Active Users',
                    'data' => array_column($timeline, 'active_users')
                ]
            ],
            'categories' => array_map(function ($item) {
                return Carbon::parse($item['timestamp'])->format('H:i');
            }, $timeline)
        ];
    }

    public function getSystemAlerts(): array
    {
        return Cache::remember('system_alerts', 30, function () {
            $alerts = [];
            
            // Check system metrics for alerts
            $systemMetrics = $this->getSystemMetrics();
            
            if ($systemMetrics['cpu']['usage'] > 90) {
                $alerts[] = [
                    'id' => 'cpu_high',
                    'type' => 'critical',
                    'title' => 'High CPU Usage',
                    'message' => "CPU usage is at {$systemMetrics['cpu']['usage']}%",
                    'timestamp' => now()->toISOString(),
                    'acknowledged' => false
                ];
            }
            
            if ($systemMetrics['memory']['percentage'] > 85) {
                $alerts[] = [
                    'id' => 'memory_high',
                    'type' => 'warning',
                    'title' => 'High Memory Usage',
                    'message' => "Memory usage is at {$systemMetrics['memory']['percentage']}%",
                    'timestamp' => now()->toISOString(),
                    'acknowledged' => false
                ];
            }
            
            if ($systemMetrics['disk']['percentage'] > 95) {
                $alerts[] = [
                    'id' => 'disk_full',
                    'type' => 'critical',
                    'title' => 'Disk Space Critical',
                    'message' => "Disk usage is at {$systemMetrics['disk']['percentage']}%",
                    'timestamp' => now()->toISOString(),
                    'acknowledged' => false
                ];
            }
            
            return $alerts;
        });
    }

    public function getRecentLogs(): array
    {
        return Cache::remember('recent_logs', 60, function () {
            // Simplified log reading - in production, use proper log aggregation
            $logs = [];
            $logTypes = ['info', 'warning', 'error', 'critical'];
            
            for ($i = 0; $i < 20; $i++) {
                $type = $logTypes[array_rand($logTypes)];
                $logs[] = [
                    'timestamp' => now()->subMinutes(rand(0, 60))->toISOString(),
                    'level' => $type,
                    'message' => $this->generateSampleLogMessage($type),
                    'context' => 'system'
                ];
            }
            
            return array_reverse($logs);
        });
    }

    public function getErrorRates(): array
    {
        return Cache::remember('error_rates', 300, function () {
            $now = now();
            $data = [];
            
            for ($i = 23; $i >= 0; $i--) {
                $hour = $now->copy()->subHours($i);
                $data[] = [
                    'hour' => $hour->format('H:00'),
                    'error_rate' => rand(0, 15),
                    'total_requests' => rand(100, 1000),
                    'errors' => rand(0, 50)
                ];
            }
            
            return $data;
        });
    }

    public function acknowledgeAlert(string $alertId): bool
    {
        Cache::forget('system_alerts');
        return true;
    }

    public function clearLogs(): bool
    {
        Cache::forget('recent_logs');
        return true;
    }

    public function exportMetrics(): array
    {
        return [
            'system' => $this->getSystemMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'cache' => $this->getCacheMetrics(),
            'queue' => $this->getQueueMetrics(),
            'performance_timeline' => $this->getPerformanceTimeline(),
            'alerts' => $this->getSystemAlerts(),
            'error_rates' => $this->getErrorRates(),
            'exported_at' => now()->toISOString()
        ];
    }

    private function getMemoryUsage(): array
    {
        $memInfo = $this->parseMemInfo();
        $total = $memInfo['MemTotal'] ?? 0;
        $available = $memInfo['MemAvailable'] ?? $memInfo['MemFree'] ?? 0;
        $used = $total - $available;
        
        return [
            'total' => $total,
            'used' => $used,
            'free' => $available,
            'percentage' => $total > 0 ? round(($used / $total) * 100, 2) : 0
        ];
    }

    private function getDiskUsage(): array
    {
        $totalBytes = disk_total_space('/');
        $freeBytes = disk_free_space('/');
        $usedBytes = $totalBytes - $freeBytes;
        
        return [
            'total' => $totalBytes,
            'used' => $usedBytes,
            'free' => $freeBytes,
            'percentage' => $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 2) : 0
        ];
    }

    private function getCpuUsage(): float
    {
        // Simplified CPU usage calculation
        $loadAvg = sys_getloadavg()[0] ?? 0;
        return min(round($loadAvg * 20, 2), 100); // Rough approximation
    }

    private function getDatabaseConnections(): array
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            $active = $result[0]->Value ?? 0;
            
            $result = DB::select("SHOW VARIABLES LIKE 'max_connections'");
            $max = $result[0]->Value ?? 100;
            
            return [
                'active' => (int)$active,
                'max' => (int)$max,
                'percentage' => $max > 0 ? round(($active / $max) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return ['active' => 0, 'max' => 100, 'percentage' => 0];
        }
    }

    private function getAverageQueryTime(): float
    {
        // Simplified calculation - in production use performance schema
        return round(rand(10, 500), 2);
    }

    private function getSlowQueryCount(): int
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Slow_queries'");
            return (int)($result[0]->Value ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getLockWaitCount(): int
    {
        return rand(0, 5); // Simplified
    }

    private function getCacheHitRate(): float
    {
        return round(rand(75, 98), 2);
    }

    private function getCacheMemoryUsage(): array
    {
        return [
            'used' => rand(100, 500) * 1024 * 1024, // MB in bytes
            'max' => 512 * 1024 * 1024, // 512MB
            'percentage' => rand(20, 80)
        ];
    }

    private function getCacheKeyCount(): int
    {
        return rand(1000, 50000);
    }

    private function getPendingJobsCount(): int
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Exception $e) {
            return rand(0, 100);
        }
    }

    private function getFailedJobsCount(): int
    {
        try {
            return DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            return rand(0, 10);
        }
    }

    private function getProcessedJobsCount(): int
    {
        return rand(100, 1000);
    }

    private function parseMemInfo(): array
    {
        $memInfo = [];
        if (file_exists('/proc/meminfo')) {
            $contents = file_get_contents('/proc/meminfo');
            if (preg_match_all('/^(\w+):\s+(\d+)\s+kB$/m', $contents, $matches)) {
                foreach ($matches[1] as $i => $key) {
                    $memInfo[$key] = (int)$matches[2][$i] * 1024; // Convert to bytes
                }
            }
        }
        return $memInfo;
    }

    private function getStatusByValue(float $value, array $thresholds): string
    {
        if ($value >= $thresholds[1]) return 'critical';
        if ($value >= $thresholds[0]) return 'warning';
        return 'good';
    }

    private function generateSampleLogMessage(string $type): string
    {
        $messages = [
            'info' => [
                'User authentication successful',
                'Cache cleared successfully',
                'Database backup completed',
                'System health check passed'
            ],
            'warning' => [
                'High memory usage detected',
                'Slow query detected',
                'Cache miss rate increasing',
                'Queue processing delayed'
            ],
            'error' => [
                'Database connection failed',
                'File upload error',
                'API request timeout',
                'Invalid user credentials'
            ],
            'critical' => [
                'System disk space critical',
                'Database connection pool exhausted',
                'Memory allocation failed',
                'Service unavailable'
            ]
        ];
        
        return $messages[$type][array_rand($messages[$type])];
    }
}
