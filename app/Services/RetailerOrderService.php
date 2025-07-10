<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Interfaces\Services\RetailerOrderServiceInterface;
use App\Interfaces\Services\OrderServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RetailerOrderService implements RetailerOrderServiceInterface
{
    protected OrderServiceInterface $orderService;

    /**
     * Create a new service instance.
     */
    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function getOrderStats(User $retailer): array
    {
        return Cache::remember("retailer_{$retailer->id}_order_stats", 300, function () use ($retailer) {
            $orders = Order::where('buyer_id', $retailer->id);

            return [
                'total_orders' => $orders->count(),
                'pending_orders' => $orders->where('status', 'pending')->count(),
                'completed_orders' => $orders->where('status', 'completed')->count(),
                'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
                'total_spent' => $orders->sum('price'),
                'average_order_value' => $orders->avg('price'),
            ];
        });
    }

    /**
     * Get recent orders for a retailer
     */
    public function getRecentOrders(User $retailer, int $limit = 10): Collection
    {
        return Order::where('buyer_id', $retailer->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOrdersByStatus(User $retailer, ?string $status = null): LengthAwarePaginator
    {
        $query = Order::where('buyer_id', $retailer->id);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getOrderTrends(User $retailer, int $days = 30): array
    {
        return Cache::remember("retailer_{$retailer->id}_order_trends_{$days}", 300, function () use ($retailer, $days) {
            $orders = Order::where('buyer_id', $retailer->id)
                ->where('created_at', '>=', now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(price) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'dates' => $orders->pluck('date'),
                'orders' => $orders->pluck('count'),
                'revenue' => $orders->pluck('total'),
            ];
        });
    }

    public function getOrderStatusDistribution(User $retailer): array
    {
        return Cache::remember("retailer_{$retailer->id}_order_status_dist", 300, function () use ($retailer) {
            return Order::where('buyer_id', $retailer->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    public function getTopOrderedProducts(User $retailer, int $limit = 10)
    {
        return Cache::remember("retailer_{$retailer->id}_top_products_{$limit}", 300, function () use ($retailer, $limit) {
            return DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.buyer_id', $retailer->id)
                ->selectRaw('products.name, SUM(order_items.quantity) as total_quantity, SUM(order_items.price * order_items.quantity) as total_value')
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_quantity', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getOrderPerformanceMetrics(User $retailer): array
    {
        return Cache::remember("retailer_{$retailer->id}_performance_metrics", 300, function () use ($retailer) {
            $thisMonth = Order::where('buyer_id', $retailer->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);

            $lastMonth = Order::where('buyer_id', $retailer->id)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year);

            $thisMonthOrders = $thisMonth->count();
            $lastMonthOrders = $lastMonth->count();
            $thisMonthRevenue = $thisMonth->sum('price');
            $lastMonthRevenue = $lastMonth->sum('price');

            return [
                'orders_growth' => $lastMonthOrders > 0
                    ? round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 2)
                    : 0,
                'revenue_growth' => $lastMonthRevenue > 0
                    ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2)
                    : 0,
                'avg_delivery_time' => $this->getAverageDeliveryTime($retailer),
                'order_fulfillment_rate' => $this->getOrderFulfillmentRate($retailer)
            ];
        });
    }

    private function getAverageDeliveryTime(User $retailer): float
    {
        $deliveredOrders = Order::where('buyer_id', $retailer->id)
            ->where('status', 'delivered')
            ->whereNotNull('completed_at')
            ->get();

        if ($deliveredOrders->isEmpty()) {
            return 0;
        }

        $totalDays = $deliveredOrders->sum(function ($order) {
            return $order->created_at->diffInDays($order->completed_at);
        });

        return round($totalDays / $deliveredOrders->count(), 1);
    }

    private function getOrderFulfillmentRate(User $retailer): float
    {
        $totalOrders = Order::where('buyer_id', $retailer->id)->count();
        if ($totalOrders === 0) return 0;

        $fulfilledOrders = Order::where('buyer_id', $retailer->id)
            ->whereIn('status', ['completed', 'delivered'])
            ->count();

        return round(($fulfilledOrders / $totalOrders) * 100, 2);
    }

    /**
     * Validate order data before creation
     */
    public function validateOrderData(array $orderData): array
    {
        $validator = Validator::make($orderData, [
            'seller_id' => 'required|integer|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'expected_delivery_date' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Check if products are available from the seller
        foreach ($orderData['items'] as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                throw ValidationException::withMessages([
                    'items' => ["Product with ID {$item['product_id']} does not exist."]
                ]);
            }

            if ($product->seller_id != $orderData['seller_id']) {
                throw ValidationException::withMessages([
                    'items' => ["Product {$product->name} is not available from the selected seller."]
                ]);
            }

            // Add product details to the item for convenience
            $item['name'] = $product->name;
            $item['price'] = $product->price;
            $item['subtotal'] = $product->price * $item['quantity'];
        }

        // Calculate total price
        $orderData['price'] = $this->calculateOrderTotal($orderData['items']);

        return $orderData;
    }

    /**
     * Create a new order for a retailer
     */
    public function createOrder(User $retailer, array $orderData): Order
    {
        // Prepare the order data
        $validatedData = $this->validateOrderData($orderData);

        $orderDetails = [
            'buyer_id' => $retailer->id,
            'seller_id' => $validatedData['seller_id'],
            'status' => Order::STATUS_PENDING,
            'price' => $validatedData['price'],
            'items' => $validatedData['items'],
            'delivery_address' => $validatedData['delivery_address'],
            'notes' => $validatedData['notes'] ?? null,
            'expected_delivery_date' => $validatedData['expected_delivery_date'] ?? null,
        ];

        // Process the order using the main order service
        $order = $this->orderService->processNewOrder($orderDetails);

        // Clear retailer's cache data
        $this->clearRetailerCache($retailer);

        return $order;
    }

    /**
     * Calculate order total and verify pricing
     */
    public function calculateOrderTotal(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];

            $product = Product::find($productId);
            if (!$product) {
                continue;
            }

            $total += $product->price * $quantity;
        }

        return $total;
    }

    /**
     * Cancel an existing order if it's in a cancellable state
     */
    public function cancelOrder(User $retailer, int $orderId): bool
    {
        $order = Order::where('id', $orderId)
            ->where('buyer_id', $retailer->id)
            ->first();

        if (!$order || !$order->canBeCancelled()) {
            return false;
        }

        $success = $this->orderService->cancelOrder($orderId);

        if ($success) {
            $this->clearRetailerCache($retailer);
        }

        return $success;
    }

    /**
     * Clear retailer's cached data
     */
    protected function clearRetailerCache(User $retailer): void
    {
        $cacheKeys = [
            "retailer_{$retailer->id}_order_stats",
            "retailer_{$retailer->id}_order_trends_30",
            "retailer_{$retailer->id}_order_status_dist",
            "retailer_{$retailer->id}_top_products_10",
            "retailer_{$retailer->id}_performance_metrics"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}
