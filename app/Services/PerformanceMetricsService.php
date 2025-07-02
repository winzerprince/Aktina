<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PerformanceMetricsService
{
    public function getResponseTimeMetrics(string $period = '24h'): array
    {
        return Cache::remember("response_time_metrics_{$period}", 300, function () use ($period) {
            $timeData = $this->generateTimeSeriesData($period);
            
            return [
                'average' => array_sum($timeData['response_times']) / count($timeData['response_times']),
                'min' => min($timeData['response_times']),
                'max' => max($timeData['response_times']),
                'p95' => $this->calculatePercentile($timeData['response_times'], 95),
                'p99' => $this->calculatePercentile($timeData['response_times'], 99),
                'timeline' => [
                    'categories' => $timeData['timestamps'],
                    'series' => [
                        [
                            'name' => 'Response Time (ms)',
                            'data' => $timeData['response_times']
                        ],
                        [
                            'name' => 'P95',
                            'data' => array_fill(0, count($timeData['timestamps']), $this->calculatePercentile($timeData['response_times'], 95))
                        ]
                    ]
                ]
            ];
        });
    }

    public function getThroughputMetrics(string $period = '24h'): array
    {
        return Cache::remember("throughput_metrics_{$period}", 300, function () use ($period) {
            $timeData = $this->generateTimeSeriesData($period);
            
            return [
                'requests_per_second' => array_sum($timeData['requests']) / count($timeData['requests']),
                'peak_rps' => max($timeData['requests']),
                'total_requests' => array_sum($timeData['requests']),
                'successful_requests' => array_sum($timeData['successful_requests']),
                'success_rate' => (array_sum($timeData['successful_requests']) / array_sum($timeData['requests'])) * 100,
                'timeline' => [
                    'categories' => $timeData['timestamps'],
                    'series' => [
                        [
                            'name' => 'Requests/sec',
                            'data' => $timeData['requests']
                        ],
                        [
                            'name' => 'Successful Requests/sec',
                            'data' => $timeData['successful_requests']
                        ]
                    ]
                ]
            ];
        });
    }

    public function getErrorMetrics(string $period = '24h'): array
    {
        return Cache::remember("error_metrics_{$period}", 300, function () use ($period) {
            $errorData = $this->generateErrorData($period);
            
            return [
                'total_errors' => array_sum($errorData['errors']),
                'error_rate' => (array_sum($errorData['errors']) / array_sum($errorData['total_requests'])) * 100,
                'error_types' => [
                    ['name' => '4xx Client Errors', 'count' => rand(10, 100)],
                    ['name' => '5xx Server Errors', 'count' => rand(5, 50)],
                    ['name' => 'Timeout Errors', 'count' => rand(1, 20)],
                    ['name' => 'Connection Errors', 'count' => rand(1, 15)]
                ],
                'timeline' => [
                    'categories' => $errorData['timestamps'],
                    'series' => [
                        [
                            'name' => 'Errors',
                            'data' => $errorData['errors']
                        ],
                        [
                            'name' => 'Error Rate %',
                            'data' => array_map(function ($errors, $total) {
                                return $total > 0 ? round(($errors / $total) * 100, 2) : 0;
                            }, $errorData['errors'], $errorData['total_requests'])
                        ]
                    ]
                ]
            ];
        });
    }

    public function getResourceUtilization(): array
    {
        return Cache::remember('resource_utilization', 60, function () {
            return [
                'cpu' => [
                    'current' => round(rand(20, 80), 2),
                    'average' => round(rand(30, 60), 2),
                    'peak' => round(rand(70, 95), 2)
                ],
                'memory' => [
                    'current' => round(rand(40, 85), 2),
                    'average' => round(rand(50, 70), 2),
                    'peak' => round(rand(80, 95), 2)
                ],
                'disk_io' => [
                    'read_iops' => rand(100, 1000),
                    'write_iops' => rand(50, 500),
                    'read_throughput' => rand(10, 100), // MB/s
                    'write_throughput' => rand(5, 50) // MB/s
                ],
                'network' => [
                    'inbound_mbps' => round(rand(10, 100), 2),
                    'outbound_mbps' => round(rand(5, 80), 2),
                    'connections' => rand(50, 200)
                ]
            ];
        });
    }

    public function getApplicationPerformance(): array
    {
        return Cache::remember('application_performance', 180, function () {
            return [
                'routes' => [
                    ['route' => '/api/orders', 'avg_time' => 245, 'calls' => 1250, 'errors' => 12],
                    ['route' => '/api/products', 'avg_time' => 180, 'calls' => 890, 'errors' => 3],
                    ['route' => '/api/users', 'avg_time' => 120, 'calls' => 650, 'errors' => 8],
                    ['route' => '/api/analytics', 'avg_time' => 890, 'calls' => 120, 'errors' => 2]
                ],
                'slowest_queries' => [
                    ['query' => 'SELECT * FROM orders JOIN...', 'avg_time' => 1250, 'count' => 45],
                    ['query' => 'SELECT * FROM products WHERE...', 'avg_time' => 890, 'count' => 78],
                    ['query' => 'UPDATE inventory SET...', 'avg_time' => 650, 'count' => 23]
                ],
                'cache_performance' => [
                    'hit_rate' => round(rand(85, 98), 2),
                    'miss_rate' => round(rand(2, 15), 2),
                    'eviction_rate' => round(rand(1, 5), 2)
                ]
            ];
        });
    }

    public function getDatabasePerformance(): array
    {
        return Cache::remember('database_performance', 180, function () {
            return [
                'connection_pool' => [
                    'active' => rand(5, 25),
                    'idle' => rand(10, 35),
                    'max' => 50,
                    'wait_time' => round(rand(0, 100), 2)
                ],
                'query_performance' => [
                    'avg_query_time' => round(rand(50, 300), 2),
                    'slow_queries' => rand(5, 50),
                    'queries_per_second' => rand(100, 500)
                ],
                'locks_and_deadlocks' => [
                    'lock_waits' => rand(0, 10),
                    'deadlocks' => rand(0, 3),
                    'lock_timeout' => rand(0, 5)
                ],
                'index_usage' => [
                    'index_scans' => rand(1000, 5000),
                    'table_scans' => rand(10, 100),
                    'index_hit_ratio' => round(rand(90, 99), 2)
                ]
            ];
        });
    }

    public function getBottleneckAnalysis(): array
    {
        return Cache::remember('bottleneck_analysis', 300, function () {
            $bottlenecks = [
                [
                    'type' => 'Database',
                    'severity' => 'High',
                    'description' => 'Slow query detected on orders table',
                    'impact' => 'Response time increased by 35%',
                    'recommendation' => 'Add index on created_at column'
                ],
                [
                    'type' => 'Memory',
                    'severity' => 'Medium',
                    'description' => 'High memory usage during peak hours',
                    'impact' => 'Increased garbage collection frequency',
                    'recommendation' => 'Optimize cache usage or increase memory allocation'
                ],
                [
                    'type' => 'API',
                    'severity' => 'Low',
                    'description' => 'External API timeout occasionally',
                    'impact' => 'Minimal user experience degradation',
                    'recommendation' => 'Implement retry mechanism with exponential backoff'
                ]
            ];

            return $bottlenecks;
        });
    }

    public function getPerformanceTrends(string $period = '24h'): array
    {
        return Cache::remember("performance_trends_{$period}", 600, function () use ($period) {
            $hours = $this->getPeriodHours($period);
            $data = [];
            
            for ($i = $hours; $i >= 0; $i--) {
                $timestamp = now()->subHours($i);
                $data[] = [
                    'timestamp' => $timestamp->format('H:i'),
                    'response_time' => rand(100, 500),
                    'throughput' => rand(50, 200),
                    'error_rate' => rand(0, 10),
                    'cpu_usage' => rand(20, 80),
                    'memory_usage' => rand(40, 85)
                ];
            }
            
            return [
                'categories' => array_column($data, 'timestamp'),
                'series' => [
                    [
                        'name' => 'Response Time (ms)',
                        'data' => array_column($data, 'response_time')
                    ],
                    [
                        'name' => 'Throughput (req/s)',
                        'data' => array_column($data, 'throughput')
                    ],
                    [
                        'name' => 'Error Rate (%)',
                        'data' => array_column($data, 'error_rate')
                    ],
                    [
                        'name' => 'CPU Usage (%)',
                        'data' => array_column($data, 'cpu_usage')
                    ],
                    [
                        'name' => 'Memory Usage (%)',
                        'data' => array_column($data, 'memory_usage')
                    ]
                ]
            ];
        });
    }

    public function generatePerformanceReport(string $period): array
    {
        return [
            'period' => $period,
            'generated_at' => now()->toISOString(),
            'response_time' => $this->getResponseTimeMetrics($period),
            'throughput' => $this->getThroughputMetrics($period),
            'errors' => $this->getErrorMetrics($period),
            'resources' => $this->getResourceUtilization(),
            'application' => $this->getApplicationPerformance(),
            'database' => $this->getDatabasePerformance(),
            'bottlenecks' => $this->getBottleneckAnalysis(),
            'trends' => $this->getPerformanceTrends($period)
        ];
    }

    public function clearCache(): void
    {
        $periods = ['1h', '6h', '24h', '7d'];
        foreach ($periods as $period) {
            Cache::forget("response_time_metrics_{$period}");
            Cache::forget("throughput_metrics_{$period}");
            Cache::forget("error_metrics_{$period}");
            Cache::forget("performance_trends_{$period}");
        }
        
        Cache::forget('resource_utilization');
        Cache::forget('application_performance');
        Cache::forget('database_performance');
        Cache::forget('bottleneck_analysis');
    }

    private function generateTimeSeriesData(string $period): array
    {
        $hours = $this->getPeriodHours($period);
        $data = [
            'timestamps' => [],
            'response_times' => [],
            'requests' => [],
            'successful_requests' => []
        ];
        
        for ($i = $hours; $i >= 0; $i--) {
            $timestamp = now()->subHours($i);
            $data['timestamps'][] = $timestamp->format('H:i');
            $data['response_times'][] = rand(50, 800);
            $requests = rand(10, 100);
            $data['requests'][] = $requests;
            $data['successful_requests'][] = $requests - rand(0, intval($requests * 0.1));
        }
        
        return $data;
    }

    private function generateErrorData(string $period): array
    {
        $hours = $this->getPeriodHours($period);
        $data = [
            'timestamps' => [],
            'errors' => [],
            'total_requests' => []
        ];
        
        for ($i = $hours; $i >= 0; $i--) {
            $timestamp = now()->subHours($i);
            $totalRequests = rand(50, 500);
            $data['timestamps'][] = $timestamp->format('H:i');
            $data['errors'][] = rand(0, intval($totalRequests * 0.1));
            $data['total_requests'][] = $totalRequests;
        }
        
        return $data;
    }

    private function getPeriodHours(string $period): int
    {
        return match ($period) {
            '1h' => 1,
            '6h' => 6,
            '24h' => 24,
            '7d' => 168,
            default => 24
        };
    }

    private function calculatePercentile(array $data, int $percentile): float
    {
        sort($data);
        $index = ($percentile / 100) * (count($data) - 1);
        
        if (floor($index) == $index) {
            return $data[$index];
        }
        
        $lower = $data[floor($index)];
        $upper = $data[ceil($index)];
        return $lower + ($upper - $lower) * ($index - floor($index));
    }
}
