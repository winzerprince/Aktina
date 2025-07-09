<?php

namespace App\Interfaces\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface VendorSalesServiceInterface
{
    /**
     * Get sales metrics for a vendor
     *
     * @param int $vendorId
     * @param string $timeframe
     * @return array
     */
    public function getSalesMetrics($vendorId, $timeframe = '30d');

    /**
     * Get sales trend data for a vendor
     *
     * @param int $vendorId
     * @param string $timeframe
     * @return Collection
     */
    public function getSalesTrend($vendorId, $timeframe = '30d');

    /**
     * Get top retailers by order value
     *
     * @param int $vendorId
     * @param string $timeframe
     * @param int $limit
     * @return Collection
     */
    public function getTopRetailers($vendorId, $timeframe = '30d', $limit = 10);

    /**
     * Get revenue breakdown by product
     *
     * @param int $vendorId
     * @param string $timeframe
     * @return Collection
     */
    public function getRevenueByProduct($vendorId, $timeframe = '30d');

    /**
     * Get progress towards sales goal
     *
     * @param int $vendorId
     * @param float $goalAmount
     * @param string $timeframe
     * @return array
     */
    public function getSalesGoalProgress($vendorId, $goalAmount, $timeframe = '30d');

    /**
     * Get retailer performance metrics
     *
     * @param int $vendorId
     * @param string $timeframe
     * @return array
     */
    public function getRetailerPerformanceMetrics($vendorId, $timeframe = '30d');

    /**
     * Get total revenue for a vendor in the given timeframe
     *
     * @param int $vendorId
     * @param Carbon $startDate
     * @return float
     */
    public function getTotalRevenue($vendorId, $startDate);

    /**
     * Get average order value for a vendor
     *
     * @param int $vendorId
     * @param Carbon $startDate
     * @return float
     */
    public function getAverageOrderValue($vendorId, $startDate);

    /**
     * Process an order status update with validation
     *
     * @param int $orderId
     * @param string $newStatus
     * @param int $vendorId
     * @param array $additionalData
     * @return bool
     */
    public function processOrderStatusUpdate($orderId, $newStatus, $vendorId, $additionalData = []);

    /**
     * Get valid next statuses for an order
     *
     * @param Order $order
     * @return array
     */
    public function getValidNextStatuses(Order $order): array;

    /**
     * Check if vendor can update order to the specified status
     *
     * @param Order $order
     * @param string $status
     * @return bool
     */
    public function canUpdateOrderStatus(Order $order, string $status): bool;
}
