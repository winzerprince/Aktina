<?php

namespace App\Repositories;

use App\Interfaces\Repositories\EnhancedOrderRepositoryInterface;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EnhancedOrderRepository implements EnhancedOrderRepositoryInterface
{
    public function getOrdersByStatus(string $status): Collection
    {
        return Order::where('status', $status)
            ->with(['buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrdersByDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->with(['buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrdersByUser(int $userId, string $role = null): Collection
    {
        $query = Order::with(['buyer', 'seller']);
        
        if ($role === 'buyer') {
            $query->where('buyer_id', $userId);
        } elseif ($role === 'seller') {
            $query->where('seller_id', $userId);
        } else {
            $query->where(function($q) use ($userId) {
                $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
            });
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getOrdersRequiringApproval(): Collection
    {
        return Order::where('status', Order::STATUS_PENDING)
            ->with(['buyer', 'seller'])
            ->where('created_at', '>', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getOrderAnalytics(Carbon $startDate, Carbon $endDate): array
    {
        $orders = $this->getOrdersByDateRange($startDate, $endDate);
        
        return [
            'total_orders' => $orders->count(),
            'total_value' => $orders->sum('price'),
            'average_order_value' => $orders->avg('price'),
            'pending_orders' => $orders->where('status', Order::STATUS_PENDING)->count(),
            'accepted_orders' => $orders->where('status', Order::STATUS_ACCEPTED)->count(),
            'completed_orders' => $orders->where('status', Order::STATUS_COMPLETE)->count(),
            'orders_by_day' => $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function($dayOrders) {
                return [
                    'count' => $dayOrders->count(),
                    'value' => $dayOrders->sum('price')
                ];
            }),
            'top_buyers' => $orders->groupBy('buyer_id')->map(function($userOrders) {
                return [
                    'user' => $userOrders->first()->buyer,
                    'order_count' => $userOrders->count(),
                    'total_value' => $userOrders->sum('price')
                ];
            })->sortByDesc('total_value')->take(10)->values()
        ];
    }

    public function updateOrderStatus(int $orderId, string $status, array $metadata = []): Order
    {
        $order = Order::findOrFail($orderId);
        
        $updateData = [
            'status' => $status,
            'updated_at' => now()
        ];
        
        // Add status-specific timestamps
        switch ($status) {
            case Order::STATUS_ACCEPTED:
                $updateData['accepted_at'] = now();
                if (isset($metadata['approver_id'])) {
                    $updateData['approver_id'] = $metadata['approver_id'];
                }
                break;
            case Order::STATUS_COMPLETE:
                $updateData['completed_at'] = now();
                break;
        }
        
        // Add any additional metadata
        if (!empty($metadata)) {
            $updateData = array_merge($updateData, $metadata);
        }
        
        $order->update($updateData);
        return $order->fresh(['buyer', 'seller']);
    }

    public function getOrderWorkflow(int $orderId): array
    {
        $order = Order::with(['buyer', 'seller'])->findOrFail($orderId);
        
        $workflow = [
            [
                'step' => 'placed',
                'status' => 'completed',
                'timestamp' => $order->created_at,
                'description' => 'Order placed by ' . $order->buyer->name
            ],
            [
                'step' => 'pending_approval',
                'status' => $order->status === Order::STATUS_PENDING ? 'current' : 'completed',
                'timestamp' => $order->created_at,
                'description' => 'Awaiting approval from ' . $order->seller->name
            ]
        ];
        
        if ($order->status !== Order::STATUS_PENDING) {
            $workflow[] = [
                'step' => 'approved',
                'status' => $order->status === Order::STATUS_ACCEPTED ? 'current' : 'completed',
                'timestamp' => $order->accepted_at ?? $order->updated_at,
                'description' => 'Order approved'
            ];
        }
        
        if ($order->status === Order::STATUS_COMPLETE) {
            $workflow[] = [
                'step' => 'completed',
                'status' => 'completed',
                'timestamp' => $order->completed_at ?? $order->updated_at,
                'description' => 'Order completed and delivered'
            ];
        }
        
        return $workflow;
    }

    public function getSupplyChainOrders(int $userId, string $role): array
    {
        $baseQuery = Order::with(['buyer', 'seller']);
        
        switch ($role) {
            case 'retailer':
                // Retailers place orders to vendors
                $incomingOrders = collect();
                $outgoingOrders = $baseQuery->where('buyer_id', $userId)->get();
                break;
                
            case 'vendor':
                // Vendors receive orders from retailers and place orders to Aktina
                $incomingOrders = $baseQuery->where('seller_id', $userId)->get();
                $outgoingOrders = $baseQuery->where('buyer_id', $userId)->get();
                break;
                
            case 'admin':
            case 'production_manager':
            case 'hr_manager':
                // Aktina roles receive orders from vendors and place orders to suppliers
                $incomingOrders = $baseQuery->whereHas('seller', function($q) {
                    $q->whereIn('role', ['admin', 'production_manager', 'hr_manager']);
                })->get();
                $outgoingOrders = $baseQuery->whereHas('buyer', function($q) {
                    $q->whereIn('role', ['admin', 'production_manager', 'hr_manager']);
                })->get();
                break;
                
            case 'supplier':
                // Suppliers receive orders from Aktina
                $incomingOrders = $baseQuery->where('seller_id', $userId)->get();
                $outgoingOrders = collect();
                break;
                
            default:
                $incomingOrders = collect();
                $outgoingOrders = collect();
        }
        
        return [
            'incoming' => $incomingOrders,
            'outgoing' => $outgoingOrders,
            'stats' => [
                'total_incoming' => $incomingOrders->count(),
                'total_outgoing' => $outgoingOrders->count(),
                'pending_incoming' => $incomingOrders->where('status', Order::STATUS_PENDING)->count(),
                'pending_outgoing' => $outgoingOrders->where('status', Order::STATUS_PENDING)->count(),
                'total_incoming_value' => $incomingOrders->sum('price'),
                'total_outgoing_value' => $outgoingOrders->sum('price')
            ]
        ];
    }

    public function getOrderValueByPeriod(string $period = 'daily'): array
    {
        $format = match($period) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            'yearly' => '%Y',
            default => '%Y-%m-%d'
        };
        
        return Order::select(
                DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(price) as total_value'),
                DB::raw('AVG(price) as average_value')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(90))
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->toArray();
    }

    public function getTopOrdersByValue(int $limit = 10): Collection
    {
        return Order::with(['buyer', 'seller'])
            ->orderBy('price', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOrdersBySupplier(int $supplierId): Collection
    {
        return Order::where('seller_id', $supplierId)
            ->with(['buyer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrdersByCustomer(int $customerId): Collection
    {
        return Order::where('buyer_id', $customerId)
            ->with(['seller'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrdersWithItems(): Collection
    {
        return Order::with(['buyer', 'seller'])
            ->whereNotNull('items')
            ->where('items', '!=', '[]')
            ->get();
    }

    public function getOrderStatusHistory(int $orderId): array
    {
        $order = Order::findOrFail($orderId);
        
        $history = [
            [
                'status' => 'placed',
                'timestamp' => $order->created_at,
                'description' => 'Order placed'
            ]
        ];
        
        if ($order->accepted_at) {
            $history[] = [
                'status' => 'accepted',
                'timestamp' => $order->accepted_at,
                'description' => 'Order accepted'
            ];
        }
        
        if ($order->completed_at) {
            $history[] = [
                'status' => 'completed',
                'timestamp' => $order->completed_at,
                'description' => 'Order completed'
            ];
        }
        
        return $history;
    }
}
