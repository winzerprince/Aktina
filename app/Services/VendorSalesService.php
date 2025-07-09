<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Retailer;
use App\Interfaces\Services\VendorSalesServiceInterface;
use App\Interfaces\Services\OrderServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class VendorSalesService implements VendorSalesServiceInterface
{
    public function getSalesMetrics($vendorId, $timeframe = '30d')
    {
        $cacheKey = "vendor_sales_metrics_{$vendorId}_{$timeframe}";

        return Cache::remember($cacheKey, 300, function () use ($vendorId, $timeframe) {
            $startDate = $this->getTimeframeDate($timeframe);

            return [
                'total_revenue' => $this->getTotalRevenue($vendorId, $startDate),
                'order_count' => $this->getOrderCount($vendorId, $startDate),
                'average_order_value' => $this->getAverageOrderValue($vendorId, $startDate),
                'growth_rate' => $this->getGrowthRate($vendorId, $startDate),
                'top_products' => $this->getTopProducts($vendorId, $startDate),
                'conversion_rate' => $this->getConversionRate($vendorId, $startDate),
            ];
        });
    }

    public function getSalesTrend($vendorId, $timeframe = '30d')
    {
        $cacheKey = "vendor_sales_trend_{$vendorId}_{$timeframe}";

        return Cache::remember($cacheKey, 300, function () use ($vendorId, $timeframe) {
            $startDate = $this->getTimeframeDate($timeframe);
            $groupBy = $this->getGroupByFormat($timeframe);

            return Order::where('seller_id', $vendorId)
                ->where('created_at', '>=', $startDate)
                ->selectRaw("DATE_FORMAT(created_at, '{$groupBy}') as period")
                ->selectRaw('SUM(price) as revenue')
                ->selectRaw('COUNT(*) as order_count')
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->period => [
                        'revenue' => (float) $item->revenue,
                        'orders' => (int) $item->order_count
                    ]];
                });
        });
    }

    public function getTopRetailers($vendorId, $timeframe = '30d', $limit = 10)
    {
        $startDate = $this->getTimeframeDate($timeframe);

        return Order::join('users', 'orders.buyer_id', '=', 'users.id')
            ->where('orders.seller_id', $vendorId)
            ->where('orders.created_at', '>=', $startDate)
            ->where('users.role', 'retailer')
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('SUM(orders.price) as total_spent')
            ->selectRaw('COUNT(orders.id) as order_count')
            ->selectRaw('AVG(orders.price) as average_order')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(function ($retailer) {
                return [
                    'id' => $retailer->id,
                    'name' => $retailer->name,
                    'email' => $retailer->email,
                    'total_spent' => (float) $retailer->total_spent,
                    'order_count' => (int) $retailer->order_count,
                    'average_order' => (float) $retailer->average_order,
                ];
            });
    }

    public function getRevenueByProduct($vendorId, $timeframe = '30d')
    {
        $startDate = $this->getTimeframeDate($timeframe);

        $orders = Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $productRevenues = [];

        foreach ($orders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;

            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? 0;
                $unitPrice = $item['unit_price'] ?? 0;

                if ($productId) {
                    if (!isset($productRevenues[$productId])) {
                        // Get product name from database
                        $product = \App\Models\Product::find($productId);
                        $productRevenues[$productId] = [
                            'product_id' => $productId,
                            'name' => $product ? $product->name : 'Unknown Product',
                            'revenue' => 0,
                            'quantity_sold' => 0,
                        ];
                    }

                    $productRevenues[$productId]['revenue'] += $quantity * $unitPrice;
                    $productRevenues[$productId]['quantity_sold'] += $quantity;
                }
            }
        }

        // Sort by revenue desc
        usort($productRevenues, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return collect($productRevenues)->map(function ($product) {
            return [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'revenue' => (float) $product['revenue'],
                'quantity_sold' => (int) $product['quantity_sold'],
            ];
        });
    }

    public function getSalesGoalProgress($vendorId, $goalAmount, $timeframe = '30d')
    {
        $currentRevenue = $this->getTotalRevenue($vendorId, $this->getTimeframeDate($timeframe));
        $progressPercentage = $goalAmount > 0 ? ($currentRevenue / $goalAmount) * 100 : 0;

        return [
            'goal_amount' => $goalAmount,
            'current_revenue' => $currentRevenue,
            'progress_percentage' => min(100, $progressPercentage),
            'remaining_amount' => max(0, $goalAmount - $currentRevenue),
        ];
    }

    public function getRetailerPerformanceMetrics($vendorId, $timeframe = '30d')
    {
        $startDate = $this->getTimeframeDate($timeframe);

        return [
            'total_retailers' => $this->getTotalActiveRetailers($vendorId, $startDate),
            'new_retailers' => $this->getNewRetailers($vendorId, $startDate),
            'repeat_customers' => $this->getRepeatCustomers($vendorId, $startDate),
            'churn_rate' => $this->getChurnRate($vendorId, $startDate),
            'retailer_satisfaction' => $this->getRetailerSatisfactionScore($vendorId, $startDate),
        ];
    }

    public function getTotalRevenue($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->sum('price') ?? 0;
    }

    private function getOrderCount($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    public function getAverageOrderValue($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->avg('price') ?? 0;
    }

    private function getGrowthRate($vendorId, $startDate)
    {
        $currentPeriodRevenue = $this->getTotalRevenue($vendorId, $startDate);
        $previousPeriodRevenue = $this->getTotalRevenue($vendorId, $this->getPreviousPeriodDate($startDate));

        if ($previousPeriodRevenue == 0) {
            return $currentPeriodRevenue > 0 ? 100 : 0;
        }

        return (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100;
    }

    private function getTopProducts($vendorId, $startDate, $limit = 5)
    {
        $orders = Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $productSales = [];

        foreach ($orders as $order) {
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;

            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? 0;

                if ($productId) {
                    if (!isset($productSales[$productId])) {
                        // Get product name from database
                        $product = \App\Models\Product::find($productId);
                        $productSales[$productId] = [
                            'name' => $product ? $product->name : 'Unknown Product',
                            'total_sold' => 0,
                        ];
                    }

                    $productSales[$productId]['total_sold'] += $quantity;
                }
            }
        }

        // Sort by total sold desc and take top N
        arsort($productSales);
        $topProducts = array_slice($productSales, 0, $limit, true);

        return array_map(function($product) {
            return $product['total_sold'];
        }, $topProducts);
    }

    private function getConversionRate($vendorId, $startDate)
    {
        // Mock implementation - would need to track visitors/leads
        return rand(15, 35) + (rand(0, 99) / 100);
    }

    private function getTotalActiveRetailers($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->distinct('buyer_id')
            ->count('buyer_id');
    }

    private function getNewRetailers($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->whereNotExists(function ($query) use ($vendorId, $startDate) {
                $query->select(DB::raw(1))
                    ->from('orders as o2')
                    ->whereColumn('o2.buyer_id', 'orders.buyer_id')
                    ->where('o2.seller_id', $vendorId)
                    ->where('o2.created_at', '<', $startDate);
            })
            ->distinct('buyer_id')
            ->count('buyer_id');
    }

    private function getRepeatCustomers($vendorId, $startDate)
    {
        return Order::where('seller_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->select('buyer_id')
            ->groupBy('buyer_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();
    }

    private function getChurnRate($vendorId, $startDate)
    {
        // Mock implementation - would need proper churn calculation
        return rand(5, 15) + (rand(0, 99) / 100);
    }

    private function getRetailerSatisfactionScore($vendorId, $startDate)
    {
        // Mock implementation - would integrate with review/rating system
        return rand(75, 95) + (rand(0, 99) / 100);
    }

    private function getTimeframeDate($timeframe)
    {
        return match($timeframe) {
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            '90d' => Carbon::now()->subDays(90),
            '1y' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(30),
        };
    }

    private function getPreviousPeriodDate($startDate)
    {
        $daysDiff = abs(intval(Carbon::now()->diffInDays($startDate)));
        return Carbon::now()->subDays($daysDiff * 2);
    }

    private function getGroupByFormat($timeframe)
    {
        return match($timeframe) {
            '7d' => '%Y-%m-%d',
            '30d' => '%Y-%m-%d',
            '90d' => '%Y-%m-%d',
            '1y' => '%Y-%m',
            default => '%Y-%m-%d',
        };
    }

    /**
     * Process an order status update with validation
     *
     * @param int $orderId
     * @param string $newStatus
     * @param int $vendorId
     * @param array $additionalData
     * @return bool
     */
    public function processOrderStatusUpdate($orderId, $newStatus, $vendorId, $additionalData = [])
    {
        try {
            $order = Order::where('id', $orderId)
                ->where('seller_id', $vendorId)
                ->first();

            if (!$order) {
                throw ValidationException::withMessages(['order' => 'Order not found or access denied']);
            }

            if (!$this->canUpdateOrderStatus($order, $newStatus)) {
                throw ValidationException::withMessages(['status' => 'Invalid status transition']);
            }

            // Start a database transaction
            return DB::transaction(function () use ($order, $newStatus, $additionalData) {
                // Update status
                $order->status = $newStatus;

                // Update timestamp based on status
                switch ($newStatus) {
                    case Order::STATUS_ACCEPTED:
                        $order->approved_at = now();
                        break;

                    case Order::STATUS_REJECTED:
                        $order->rejected_at = now();
                        break;

                    case Order::STATUS_PROCESSING:
                        $order->fulfillment_started_at = now();
                        break;

                    case Order::STATUS_PARTIALLY_FULFILLED:
                        if (!isset($order->fulfillment_data['partial_fulfillment_at'])) {
                            $fulfillmentData = $order->fulfillment_data ?: [];
                            $fulfillmentData['partial_fulfillment_at'] = now()->toDateTimeString();
                            $order->fulfillment_data = $fulfillmentData;
                        }
                        break;

                    case Order::STATUS_FULFILLED:
                        $fulfillmentData = $order->fulfillment_data ?: [];
                        $fulfillmentData['fulfilled_at'] = now()->toDateTimeString();
                        $order->fulfillment_data = $fulfillmentData;
                        break;

                    case Order::STATUS_SHIPPED:
                        $order->shipped_at = now();
                        // Add tracking number if provided
                        if (!empty($additionalData['tracking_number'])) {
                            $fulfillmentData = $order->fulfillment_data ?: [];
                            $fulfillmentData['tracking_number'] = $additionalData['tracking_number'];
                            $fulfillmentData['shipping_provider'] = $additionalData['shipping_provider'] ?? null;
                            $order->fulfillment_data = $fulfillmentData;
                        }
                        break;

                    case Order::STATUS_IN_TRANSIT:
                        $fulfillmentData = $order->fulfillment_data ?: [];
                        $fulfillmentData['in_transit_at'] = now()->toDateTimeString();
                        $order->fulfillment_data = $fulfillmentData;
                        break;

                    case Order::STATUS_DELIVERED:
                        $fulfillmentData = $order->fulfillment_data ?: [];
                        $fulfillmentData['delivered_at'] = now()->toDateTimeString();
                        $order->fulfillment_data = $fulfillmentData;
                        break;

                    case Order::STATUS_COMPLETE:
                        $order->completed_at = now();
                        break;

                    case Order::STATUS_RETURNED:
                        $fulfillmentData = $order->fulfillment_data ?: [];
                        $fulfillmentData['returned_at'] = now()->toDateTimeString();
                        if (!empty($additionalData['return_reason'])) {
                            $fulfillmentData['return_reason'] = $additionalData['return_reason'];
                        }
                        $order->fulfillment_data = $fulfillmentData;
                        break;

                    case Order::STATUS_FULFILLMENT_FAILED:
                        $order->fulfillment_failed_at = now();
                        if (!empty($additionalData['failure_reason'])) {
                            $fulfillmentData = $order->fulfillment_data ?: [];
                            $fulfillmentData['failure_reason'] = $additionalData['failure_reason'];
                            $order->fulfillment_data = $fulfillmentData;
                        }
                        break;
                }

                // Save all changes
                $order->save();

                // Clear cache for this vendor
                $this->clearVendorCache($order->seller_id);

                // Trigger notifications (will implement in a separate step)

                return true;
            });
        } catch (\Exception $e) {
            Log::error('Order status update failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'new_status' => $newStatus,
                'vendor_id' => $vendorId
            ]);

            throw $e;
        }
    }

    /**
     * Get valid next statuses for an order
     *
     * @param Order $order
     * @return array
     */
    public function getValidNextStatuses(Order $order): array
    {
        $currentStatus = $order->status;
        $validStatuses = [];

        switch ($currentStatus) {
            case Order::STATUS_PENDING:
                $validStatuses = [
                    Order::STATUS_ACCEPTED => 'Accept Order',
                    Order::STATUS_REJECTED => 'Reject Order'
                ];
                break;

            case Order::STATUS_ACCEPTED:
                $validStatuses = [
                    Order::STATUS_PROCESSING => 'Start Processing',
                    Order::STATUS_CANCELLED => 'Cancel Order'
                ];
                break;

            case Order::STATUS_PROCESSING:
                $validStatuses = [
                    Order::STATUS_PARTIALLY_FULFILLED => 'Partially Fulfill',
                    Order::STATUS_FULFILLED => 'Mark as Fulfilled',
                    Order::STATUS_FULFILLMENT_FAILED => 'Fulfillment Failed'
                ];
                break;

            case Order::STATUS_PARTIALLY_FULFILLED:
                $validStatuses = [
                    Order::STATUS_FULFILLED => 'Complete Fulfillment',
                    Order::STATUS_FULFILLMENT_FAILED => 'Fulfillment Failed'
                ];
                break;

            case Order::STATUS_FULFILLED:
                $validStatuses = [
                    Order::STATUS_SHIPPED => 'Ship Order'
                ];
                break;

            case Order::STATUS_FULFILLMENT_FAILED:
                $validStatuses = [
                    Order::STATUS_PROCESSING => 'Retry Processing',
                    Order::STATUS_CANCELLED => 'Cancel Order'
                ];
                break;

            case Order::STATUS_SHIPPED:
                $validStatuses = [
                    Order::STATUS_IN_TRANSIT => 'Mark as In Transit',
                    Order::STATUS_DELIVERED => 'Mark as Delivered',
                    Order::STATUS_RETURNED => 'Return to Sender'
                ];
                break;

            case Order::STATUS_IN_TRANSIT:
                $validStatuses = [
                    Order::STATUS_DELIVERED => 'Mark as Delivered',
                    Order::STATUS_RETURNED => 'Return to Sender'
                ];
                break;

            case Order::STATUS_DELIVERED:
                $validStatuses = [
                    Order::STATUS_COMPLETE => 'Complete Order',
                    Order::STATUS_RETURNED => 'Mark as Returned'
                ];
                break;
        }

        return $validStatuses;
    }

    /**
     * Check if vendor can update order to the specified status
     *
     * @param Order $order
     * @param string $status
     * @return bool
     */
    public function canUpdateOrderStatus(Order $order, string $status): bool
    {
        $validNextStatuses = $this->getValidNextStatuses($order);
        return array_key_exists($status, $validNextStatuses);
    }

    /**
     * Clear cache for a vendor
     *
     * @param int $vendorId
     */
    protected function clearVendorCache($vendorId): void
    {
        $cacheKeys = [
            "vendor_sales_metrics_{$vendorId}_30d",
            "vendor_sales_metrics_{$vendorId}_7d",
            "vendor_sales_metrics_{$vendorId}_90d",
            "vendor_sales_trend_{$vendorId}_30d",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}
