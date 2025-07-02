<?php

namespace App\Jobs;

use App\Interfaces\Services\EnhancedOrderServiceInterface;
use App\Interfaces\Services\InventoryServiceInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateOrderAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Carbon $startDate,
        private Carbon $endDate,
        private ?string $reportType = 'daily'
    ) {}

    public function handle(
        EnhancedOrderServiceInterface $orderService,
        InventoryServiceInterface $inventoryService
    ): void {
        try {
            Log::info("Starting order analytics generation", [
                'start_date' => $this->startDate->format('Y-m-d'),
                'end_date' => $this->endDate->format('Y-m-d'),
                'report_type' => $this->reportType
            ]);
            
            // Generate comprehensive order analytics
            $analytics = $orderService->getOrderAnalytics($this->startDate, $this->endDate);
            
            // Generate additional metrics
            $metrics = [
                'period' => $this->reportType,
                'generated_at' => now(),
                'order_analytics' => $analytics,
                'inventory_impact' => $this->calculateInventoryImpact($inventoryService),
                'performance_metrics' => $this->calculatePerformanceMetrics($analytics),
                'trends' => $this->analyzeTrends($analytics)
            ];
            
            // Store or cache the analytics results
            $this->storeAnalytics($metrics);
            
            Log::info("Order analytics generation completed", [
                'total_orders' => $analytics['total_orders'],
                'total_value' => $analytics['total_value']
            ]);
            
        } catch (\Exception $e) {
            Log::error("Order analytics generation failed", [
                'start_date' => $this->startDate->format('Y-m-d'),
                'end_date' => $this->endDate->format('Y-m-d'),
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    private function calculateInventoryImpact(InventoryServiceInterface $inventoryService): array
    {
        return [
            'total_reserved_value' => 0, // Calculate based on order items
            'avg_fulfillment_time' => 0, // Calculate average time from order to fulfillment
            'stock_turnover_rate' => 0 // Calculate inventory turnover
        ];
    }

    private function calculatePerformanceMetrics(array $analytics): array
    {
        $totalOrders = $analytics['total_orders'];
        $pendingOrders = $analytics['pending_orders'];
        $completedOrders = $analytics['completed_orders'];
        
        return [
            'completion_rate' => $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0,
            'pending_rate' => $totalOrders > 0 ? ($pendingOrders / $totalOrders) * 100 : 0,
            'average_order_value' => $analytics['average_order_value'],
            'order_velocity' => $this->calculateOrderVelocity($analytics['orders_by_day'])
        ];
    }

    private function calculateOrderVelocity(array $ordersByDay): float
    {
        if (empty($ordersByDay)) {
            return 0;
        }
        
        $dailyCounts = array_map(fn($day) => $day['count'], $ordersByDay);
        return array_sum($dailyCounts) / count($dailyCounts);
    }

    private function analyzeTrends(array $analytics): array
    {
        $ordersByDay = $analytics['orders_by_day'];
        
        if (count($ordersByDay) < 2) {
            return ['trend' => 'insufficient_data'];
        }
        
        $values = array_values($ordersByDay);
        $firstHalf = array_slice($values, 0, (int)(count($values) / 2));
        $secondHalf = array_slice($values, (int)(count($values) / 2));
        
        $firstHalfAvg = array_sum(array_map(fn($day) => $day['count'], $firstHalf)) / count($firstHalf);
        $secondHalfAvg = array_sum(array_map(fn($day) => $day['count'], $secondHalf)) / count($secondHalf);
        
        $percentChange = $firstHalfAvg > 0 ? (($secondHalfAvg - $firstHalfAvg) / $firstHalfAvg) * 100 : 0;
        
        return [
            'trend' => $percentChange > 5 ? 'increasing' : ($percentChange < -5 ? 'decreasing' : 'stable'),
            'percent_change' => round($percentChange, 2),
            'first_half_avg' => round($firstHalfAvg, 2),
            'second_half_avg' => round($secondHalfAvg, 2)
        ];
    }

    private function storeAnalytics(array $metrics): void
    {
        // Store in cache for quick access
        $cacheKey = "order_analytics_{$this->reportType}_{$this->startDate->format('Y-m-d')}_{$this->endDate->format('Y-m-d')}";
        cache()->put($cacheKey, $metrics, now()->addHours(6));
        
        // Could also store in database for historical tracking
        // DB::table('order_analytics')->insert($metrics);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateOrderAnalyticsJob failed", [
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'report_type' => $this->reportType,
            'exception' => $exception->getMessage()
        ]);
    }
}
