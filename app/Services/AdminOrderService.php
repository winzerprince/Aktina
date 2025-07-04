<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminOrderService
{
    public function getOrderStatistics($dateRange = '30_days', $statusFilter = 'all')
    {
        $dates = $this->getDateRange($dateRange);
        
        $query = Order::whereBetween('created_at', [$dates['start'], $dates['end']]);
        
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        
        $totalOrders = $query->count();
        
        return [
            'total' => $totalOrders,
            'pending' => Order::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->where('status', 'pending')->count(),
            'processing' => Order::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->where('status', 'processing')->count(),
            'completed' => Order::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->where('status', 'completed')->count(),
            'cancelled' => Order::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->where('status', 'cancelled')->count(),
            'total_revenue' => Order::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->where('status', 'complete')->sum('price'),
            'average_order_value' => Order::whereBetween('created_at', [$dates['start'], $dates['end']])
                ->where('status', 'complete')->avg('price') ?? 0,
            'completion_rate' => $totalOrders > 0 ? 
                (Order::whereBetween('created_at', [$dates['start'], $dates['end']])
                    ->where('status', 'complete')->count() / $totalOrders) * 100 : 0,
            'daily_average' => $totalOrders / max(1, $dates['end']->diffInDays($dates['start'])),
            'by_priority' => $this->getOrdersByPriority($dates),
            'top_customers' => $this->getTopCustomers($dates),
            'recent_orders' => $this->getRecentOrders(10)
        ];
    }
    
    public function updateOrderStatus($orderId, $status)
    {
        return DB::transaction(function () use ($orderId, $status) {
            $order = Order::findOrFail($orderId);
            
            $oldStatus = $order->status;
            $order->update(['status' => $status]);
            
            // Log the status change
            $this->logOrderAction('status_changed', $orderId, [
                'old_status' => $oldStatus,
                'new_status' => $status
            ]);
            
            // Send notifications if needed
            $this->handleStatusChangeNotifications($order, $oldStatus, $status);
            
            return $order;
        });
    }
    
    public function updateOrderPriority($orderId, $priority)
    {
        return DB::transaction(function () use ($orderId, $priority) {
            $order = Order::findOrFail($orderId);
            
            $oldPriority = $order->priority ?? 'normal';
            $order->update(['priority' => $priority]);
            
            // Log the priority change
            $this->logOrderAction('priority_changed', $orderId, [
                'old_priority' => $oldPriority,
                'new_priority' => $priority
            ]);
            
            return $order;
        });
    }
    
    public function bulkAction(array $orderIds, $action, $value = null)
    {
        return DB::transaction(function () use ($orderIds, $action, $value) {
            $count = 0;
            
            foreach ($orderIds as $orderId) {
                try {
                    switch ($action) {
                        case 'update_status':
                            $this->updateOrderStatus($orderId, $value);
                            break;
                        case 'update_priority':
                            $this->updateOrderPriority($orderId, $value);
                            break;
                        case 'export':
                            // This will be handled separately
                            break;
                    }
                    $count++;
                } catch (\Exception $e) {
                    \Log::error("Bulk action failed for order {$orderId}: " . $e->getMessage());
                }
            }
            
            return $count;
        });
    }
    
    public function exportOrders($format = 'csv', $filters = [])
    {
        $query = Order::with(['user', 'orderItems.product']);
        
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('user', function ($userQuery) use ($filters) {
                      $userQuery->where('name', 'like', '%' . $filters['search'] . '%')
                               ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
            $query->where('priority', $filters['priority']);
        }
        
        if (!empty($filters['date_range']) && $filters['date_range'] !== 'all') {
            $dates = $this->getDateRange($filters['date_range']);
            $query->whereBetween('created_at', [$dates['start'], $dates['end']]);
        }
        
        $orders = $query->get();
        $fileName = 'orders_export_' . date('Y-m-d_H-i-s') . '.' . $format;
        $filePath = storage_path('app/exports/' . $fileName);
        
        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        if ($format === 'csv') {
            $this->exportToCsv($orders, $filePath);
        } else {
            $this->exportToJson($orders, $filePath);
        }
        
        return $fileName;
    }
    
    public function generateOrderReport($type, $dateRange)
    {
        $stats = $this->getOrderStatistics($dateRange);
        $fileName = $type . '_order_report_' . date('Y-m-d_H-i-s') . '.pdf';
        $filePath = storage_path('app/reports/' . $fileName);
        
        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        // Generate report (mock implementation)
        file_put_contents($filePath, json_encode($stats, JSON_PRETTY_PRINT));
        
        return $fileName;
    }
    
    protected function getDateRange($range)
    {
        $endDate = Carbon::now();
        
        switch ($range) {
            case '7_days':
                $startDate = $endDate->copy()->subDays(7);
                break;
            case '30_days':
                $startDate = $endDate->copy()->subDays(30);
                break;
            case '90_days':
                $startDate = $endDate->copy()->subDays(90);
                break;
            case '1_year':
                $startDate = $endDate->copy()->subYear();
                break;
            default:
                $startDate = $endDate->copy()->subDays(30);
        }
        
        return [
            'start' => $startDate,
            'end' => $endDate
        ];
    }
    
    protected function getOrdersByPriority($dates)
    {
        return Order::whereBetween('created_at', [$dates['start'], $dates['end']])
            ->groupBy('priority')
            ->selectRaw('priority, count(*) as count')
            ->pluck('count', 'priority')
            ->toArray();
    }
    
    protected function getTopCustomers($dates, $limit = 5)
    {
        return Order::whereBetween('created_at', [$dates['start'], $dates['end']])
            ->with('user')
            ->groupBy('user_id')
            ->selectRaw('user_id, count(*) as order_count, sum(total_amount) as total_spent')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'user' => $order->user,
                    'order_count' => $order->order_count,
                    'total_spent' => $order->total_spent
                ];
            });
    }
    
    protected function getRecentOrders($limit = 10)
    {
        return Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    protected function logOrderAction($action, $orderId, $details = [])
    {
        \Log::info("Order action: {$action}", [
            'order_id' => $orderId,
            'admin_id' => auth()->id(),
            'details' => $details,
            'timestamp' => now()
        ]);
    }
    
    protected function handleStatusChangeNotifications($order, $oldStatus, $newStatus)
    {
        // Implement notification logic here
        // For now, just log the change
        \Log::info("Order status changed notification", [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);
    }
    
    protected function exportToCsv($orders, $filePath)
    {
        $handle = fopen($filePath, 'w');
        
        // Headers
        fputcsv($handle, [
            'Order ID',
            'Order Number',
            'Customer Name',
            'Customer Email',
            'Status',
            'Priority',
            'Total Amount',
            'Items Count',
            'Created At',
            'Updated At'
        ]);
        
        // Data
        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->id,
                $order->order_number,
                $order->user->name ?? 'N/A',
                $order->user->email ?? 'N/A',
                $order->status,
                $order->priority ?? 'normal',
                $order->total_amount,
                $order->orderItems->count(),
                $order->created_at?->format('Y-m-d H:i:s'),
                $order->updated_at?->format('Y-m-d H:i:s')
            ]);
        }
        
        fclose($handle);
    }
    
    protected function exportToJson($orders, $filePath)
    {
        $data = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer' => [
                    'name' => $order->user->name ?? 'N/A',
                    'email' => $order->user->email ?? 'N/A'
                ],
                'status' => $order->status,
                'priority' => $order->priority ?? 'normal',
                'total_amount' => $order->total_amount,
                'items_count' => $order->orderItems->count(),
                'created_at' => $order->created_at?->toISOString(),
                'updated_at' => $order->updated_at?->toISOString()
            ];
        });
        
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
