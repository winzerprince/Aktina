<?php

namespace App\Livewire\Vendor;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\VendorSalesService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class VendorOrderManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $dateRange = '30';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $selectedOrders = [];
    public $showOrderDetails = false;
    public $selectedOrder = null;

    protected $queryString = ['search', 'statusFilter', 'dateRange'];

    public function mount()
    {
        $this->resetPage();
    }

    #[On('order-updated')]
    public function refreshOrders()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function viewOrderDetails($orderId)
    {
        $this->selectedOrder = Order::with(['user', 'orderItems.product', 'approvals'])
            ->find($orderId);
        $this->showOrderDetails = true;
    }

    public function closeOrderDetails()
    {
        $this->showOrderDetails = false;
        $this->selectedOrder = null;
    }

    public function updateOrderStatus($orderId, $status)
    {
        try {
            $order = Order::find($orderId);
            if ($order) {
                $order->update(['status' => $status]);
                $this->dispatch('order-updated');
                session()->flash('success', 'Order status updated successfully');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    public function bulkUpdateStatus($status)
    {
        try {
            if (empty($this->selectedOrders)) {
                session()->flash('error', 'Please select orders to update');
                return;
            }

            Order::whereIn('id', $this->selectedOrders)
                ->update(['status' => $status]);

            $this->selectedOrders = [];
            $this->dispatch('order-updated');
            session()->flash('success', 'Selected orders updated successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update orders: ' . $e->getMessage());
        }
    }

    public function exportOrders()
    {
        try {
            $orders = $this->getOrdersQuery()->get();
            
            $filename = 'vendor_orders_' . now()->format('Y_m_d') . '.csv';
            $headers = ['order_id', 'customer', 'status', 'total_amount', 'items_count', 'created_at'];
            
            $csv = $orders->map(function ($order) {
                return [
                    $order->id,
                    $order->user->name,
                    $order->status,
                    number_format($order->total_amount, 2),
                    $order->order_items_count,
                    $order->created_at->format('Y-m-d H:i:s'),
                ];
            });

            session()->flash('success', 'Orders exported successfully');
            $this->dispatch('download-csv', [
                'filename' => $filename,
                'headers' => $headers,
                'data' => $csv->toArray()
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export orders: ' . $e->getMessage());
        }
    }

    public function getOrdersQuery()
    {
        $query = Order::with(['user', 'orderItems'])
            ->withCount('orderItems');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                               ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply date range filter
        if ($this->dateRange !== 'all') {
            $days = (int) $this->dateRange;
            $query->where('created_at', '>=', now()->subDays($days));
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query;
    }

    public function getOrderStatusCounts()
    {
        $baseQuery = Order::query();
        
        if ($this->dateRange !== 'all') {
            $days = (int) $this->dateRange;
            $baseQuery->where('created_at', '>=', now()->subDays($days));
        }

        return [
            'total' => $baseQuery->count(),
            'pending' => $baseQuery->clone()->where('status', 'pending')->count(),
            'processing' => $baseQuery->clone()->where('status', 'processing')->count(),
            'shipped' => $baseQuery->clone()->where('status', 'shipped')->count(),
            'delivered' => $baseQuery->clone()->where('status', 'delivered')->count(),
            'cancelled' => $baseQuery->clone()->where('status', 'cancelled')->count(),
        ];
    }

    public function getOrderMetrics()
    {
        $vendorSalesService = new VendorSalesService();
        
        try {
            return [
                'total_revenue' => $vendorSalesService->getTotalRevenue(),
                'average_order_value' => $vendorSalesService->getAverageOrderValue(),
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'fulfillment_rate' => $vendorSalesService->getOrderFulfillmentRate(),
            ];
        } catch (\Exception $e) {
            return [
                'total_revenue' => 0,
                'average_order_value' => 0,
                'orders_today' => 0,
                'fulfillment_rate' => 0,
            ];
        }
    }

    public function render()
    {
        $orders = $this->getOrdersQuery()->paginate(15);
        $statusCounts = $this->getOrderStatusCounts();
        $metrics = $this->getOrderMetrics();

        return view('livewire.vendor.vendor-order-management', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'metrics' => $metrics,
        ]);
    }
}
