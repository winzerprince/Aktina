<?php

namespace App\Repositories;

use App\Interfaces\Repositories\SalesRepositoryInterface;
use App\Models\Order;
use App\Models\ProductionManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SalesRepository implements SalesRepositoryInterface
{
    /**
     * Cache TTL for production manager user IDs (1 hour)
     */
    private const PRODUCTION_MANAGER_CACHE_TTL = 3600;

    /**
     * Cache TTL for order queries (15 minutes)
     */
    private const ORDER_CACHE_TTL = 900;

    /**
     * Get orders from production managers within date range
     */
    public function getProductionManagerOrders(Carbon $startDate, Carbon $endDate): Collection
    {
        $productionManagerUserIds = $this->getProductionManagerUserIds();

        // Generate cache key based on date range and user IDs
        $cacheKey = $this->generateOrdersCacheKey($productionManagerUserIds, $startDate, $endDate);

        return Cache::remember($cacheKey, self::ORDER_CACHE_TTL, function () use ($productionManagerUserIds, $startDate, $endDate) {
            return Order::query()
                ->whereIn('seller_id', $productionManagerUserIds)
                ->with(['buyer', 'seller'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get all orders from production managers (no date filtering)
     */
    public function getAllProductionManagerOrders(): Collection
    {
        $productionManagerUserIds = $this->getProductionManagerUserIds();

        // Generate cache key for all orders
        $cacheKey = $this->generateAllOrdersCacheKey($productionManagerUserIds);

        return Cache::remember($cacheKey, self::ORDER_CACHE_TTL, function () use ($productionManagerUserIds) {
            return Order::query()
                ->whereIn('seller_id', $productionManagerUserIds)
                ->with(['buyer', 'seller'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get orders with flexible filtering options
     */
    public function getOrdersByDateRange(array $filters, Carbon $startDate, Carbon $endDate): Collection
    {
        $query = Order::query()->whereBetween('created_at', [$startDate, $endDate]);

        // Apply dynamic filters
        if (!empty($filters['seller_ids'])) {
            $query->whereIn('seller_id', $filters['seller_ids']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Add product filtering if needed
        if (!empty($filters['product_ids'])) {
            $query->whereHas('products', function ($q) use ($filters) {
                $q->whereIn('id', $filters['product_ids']);
            });
        }

        // Add eager loading conditionally
        $eagerLoads = ['buyer', 'seller'];
        if (!empty($filters['with_products'])) {
            $eagerLoads[] = 'products';
        }

        // Generate cache key for filtered queries
        $cacheKey = $this->generateFilteredOrdersCacheKey($filters, $startDate, $endDate);

        return Cache::remember($cacheKey, self::ORDER_CACHE_TTL, function () use ($query, $eagerLoads) {
            return $query->with($eagerLoads)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get production managers user IDs for caching
     */
    public function getProductionManagerUserIds(): array
    {
        return Cache::remember('production_manager_user_ids', self::PRODUCTION_MANAGER_CACHE_TTL, function () {
            return ProductionManager::pluck('user_id')->toArray();
        });
    }

    /**
     * Generate cache key for sales orders query
     */
    private function generateOrdersCacheKey(array $userIds, Carbon $startDate, Carbon $endDate): string
    {
        $userIdsHash = md5(json_encode($userIds));
        return "sales:production_managers:{$userIdsHash}:{$startDate->format('Y-m-d')}:{$endDate->format('Y-m-d')}";
    }

    /**
     * Generate cache key for all sales orders query
     */
    private function generateAllOrdersCacheKey(array $userIds): string
    {
        $userIdsHash = md5(json_encode($userIds));
        return "sales:production_managers_all:{$userIdsHash}";
    }

    /**
     * Generate cache key for filtered sales orders query
     */
    private function generateFilteredOrdersCacheKey(array $filters, Carbon $startDate, Carbon $endDate): string
    {
        $filtersHash = md5(json_encode($filters));
        return "sales:filtered:{$filtersHash}:{$startDate->format('Y-m-d')}:{$endDate->format('Y-m-d')}";
    }

    /**
     * Clear cache for production manager sales orders
     */
    public function clearProductionManagerOrdersCache(): void
    {
        Cache::forget('production_manager_user_ids');

        // Clear pattern-based cache keys (if using Redis or similar)
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            Cache::getStore()->getRedis()->del(Cache::getStore()->getRedis()->keys('sales:production_managers:*'));
            Cache::getStore()->getRedis()->del(Cache::getStore()->getRedis()->keys('sales:production_managers_all:*'));
            Cache::getStore()->getRedis()->del(Cache::getStore()->getRedis()->keys('sales:filtered:*'));
        }
    }
}
