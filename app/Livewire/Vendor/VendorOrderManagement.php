<?php

namespace App\Livewire\Vendor;

use App\Models\Order;
use App\Interfaces\Services\VendorSalesServiceInterface;
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
    public $showOrderFulfillment = false;
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
        $this->selectedOrder = Order::with(['buyer', 'seller'])
            ->find($orderId);
        $this->showOrderDetails = true;
    }

    public function closeOrderDetails()
    {
        $this->showOrderDetails = false;
        $this->showOrderFulfillment = false;
        $this->selectedOrder = null;
    }

    public function openOrderFulfillment($orderId)
    {
        $this->showOrderDetails = false;
        $this->showOrderFulfillment = true;
        $this->selectedOrder = $orderId;
    }

    public function updateOrderStatus($orderId, $status)
    {
        try {
            $vendorSalesService = app(VendorSalesServiceInterface::class);
            $order = Order::find($orderId);

            if (!$order || $order->seller_id != auth()->id()) {
                session()->flash('error', 'Order not found or access denied');
                return;
            }

            if (!$vendorSalesService->canUpdateOrderStatus($order, $status)) {
                session()->flash('error', 'Invalid status transition. Please use the fulfillment wizard for proper status updates.');
                return;
            }

            $success = $vendorSalesService->processOrderStatusUpdate($orderId, $status, auth()->id());

            if ($success) {
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

            $vendorSalesService = app(VendorSalesServiceInterface::class);
            $vendorId = auth()->id();
            $successCount = 0;

            // Process each order individually to validate status transitions
            foreach ($this->selectedOrders as $orderId) {
                try {
                    $order = Order::find($orderId);

                    // Skip if order doesn't belong to this vendor or status transition is invalid
                    if (!$order || $order->seller_id != $vendorId || !$vendorSalesService->canUpdateOrderStatus($order, $status)) {
                        continue;
                    }

                    $success = $vendorSalesService->processOrderStatusUpdate($orderId, $status, $vendorId);
                    if ($success) $successCount++;
                } catch (\Exception $e) {
                    // Log individual failures but continue processing other orders
                    \Log::warning("Failed to update order #{$orderId}: " . $e->getMessage());
                }
            }

            $this->selectedOrders = [];
            $this->dispatch('order-updated');

            if ($successCount > 0) {
                session()->flash('success', "{$successCount} orders updated successfully");
            } else {
                session()->flash('error', 'No orders were updated. Please check status transition rules.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update orders: ' . $e->getMessage());
        }
    }

    public function exportOrders()
    {
        try {
            $orders = $this->getOrdersQuery()->get();

            $filename = 'vendor_orders_' . now()->format('Y_m_d') . '.csv';
            $headers = ['order_id', 'customer', 'status', 'price', 'items_count', 'created_at'];

            $csv = $orders->map(function ($order) {
                // Count items from JSON
                $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                $itemsCount = is_array($items) ? count($items) : 0;

                return [
                    $order->id,
                    $order->buyer->name,
                    $order->status,
                    number_format($order->price, 2),
                    $itemsCount,
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
        $vendorId = auth()->id();
        $query = Order::with(['buyer', 'seller'])
            ->where('seller_id', $vendorId);

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('buyer', function ($userQuery) {
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
        $vendorId = auth()->id();
        $baseQuery = Order::where('seller_id', $vendorId);

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
        $vendorId = auth()->id();
        $vendorSalesService = app(VendorSalesServiceInterface::class);

        try {
            $startDate = now()->subDays(30);
            return [
                'total_revenue' => $vendorSalesService->getTotalRevenue($vendorId, $startDate),
                'average_order_value' => $vendorSalesService->getAverageOrderValue($vendorId, $startDate),
                'orders_today' => Order::where('seller_id', $vendorId)->whereDate('created_at', today())->count(),
                'fulfillment_rate' => $this->getOrderFulfillmentRate($vendorId),
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

    private function getOrderFulfillmentRate($vendorId)
    {
        $totalOrders = Order::where('seller_id', $vendorId)->count();
        $fulfilledOrders = Order::where('seller_id', $vendorId)
            ->whereIn('status', ['complete', 'delivered'])->count();

        return $totalOrders > 0 ? ($fulfilledOrders / $totalOrders) * 100 : 0;
    }

    public function render()
    {
        $orders = $this->getOrdersQuery()->paginate(15);
        $statusCounts = $this->getOrderStatusCounts();
        $metrics = $this->getOrderMetrics();

        // Get valid next statuses for the first selected order (for bulk actions)
        $validBulkStatuses = [];
        if (!empty($this->selectedOrders) && count($this->selectedOrders) > 0) {
            $firstOrderId = reset($this->selectedOrders);
            $order = Order::find($firstOrderId);
            if ($order) {
                $vendorSalesService = app(VendorSalesServiceInterface::class);
                $validBulkStatuses = $vendorSalesService->getValidNextStatuses($order);
            }
        }

        return view('livewire.vendor.vendor-order-management', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'metrics' => $metrics,
            'validBulkStatuses' => $validBulkStatuses,
        ]);
    }
}
