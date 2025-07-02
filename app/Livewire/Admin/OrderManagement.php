<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Services\AdminOrderService;
use Illuminate\Support\Facades\Cache;

class OrderManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $statusFilter = 'all';
    public $priorityFilter = 'all';
    public $dateRange = '30_days';
    public $perPage = 15;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Modal states
    public $showOrderModal = false;
    public $showBulkActions = false;
    public $selectedOrders = [];
    
    // Order details
    public $selectedOrder = null;
    
    // Statistics
    public $orderStats = [];
    
    protected $listeners = [
        'refreshOrders' => 'loadOrderStats',
        'orderUpdated' => 'refreshOrders',
        'bulkActionCompleted' => 'refreshOrders'
    ];

    public function mount()
    {
        $this->loadOrderStats();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
        $this->loadOrderStats();
    }
    
    public function updatedDateRange()
    {
        $this->resetPage();
        $this->loadOrderStats();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->resetPage();
    }
    
    public function loadOrderStats()
    {
        $cacheKey = 'order_stats_' . md5($this->statusFilter . $this->dateRange);
        
        $this->orderStats = Cache::remember($cacheKey, 300, function () {
            $service = app(AdminOrderService::class);
            return $service->getOrderStatistics($this->dateRange, $this->statusFilter);
        });
    }
    
    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with(['user', 'orderItems.product'])->findOrFail($orderId);
        $this->showOrderModal = true;
    }
    
    public function closeOrderModal()
    {
        $this->showOrderModal = false;
        $this->selectedOrder = null;
    }
    
    public function updateOrderStatus($orderId, $status)
    {
        try {
            $service = app(AdminOrderService::class);
            $service->updateOrderStatus($orderId, $status);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Order status updated successfully!'
            ]);
            
            $this->loadOrderStats();
            $this->clearOrderCache();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function updateOrderPriority($orderId, $priority)
    {
        try {
            $service = app(AdminOrderService::class);
            $service->updateOrderPriority($orderId, $priority);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Order priority updated successfully!'
            ]);
            
            $this->clearOrderCache();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function toggleOrderSelection($orderId)
    {
        if (in_array($orderId, $this->selectedOrders)) {
            $this->selectedOrders = array_diff($this->selectedOrders, [$orderId]);
        } else {
            $this->selectedOrders[] = $orderId;
        }
        
        $this->showBulkActions = count($this->selectedOrders) > 0;
    }
    
    public function selectAllOrders()
    {
        $orders = $this->getOrders();
        $this->selectedOrders = $orders->pluck('id')->toArray();
        $this->showBulkActions = true;
    }
    
    public function clearSelection()
    {
        $this->selectedOrders = [];
        $this->showBulkActions = false;
    }
    
    public function bulkUpdateStatus($status)
    {
        $this->executeBulkAction('update_status', $status);
    }
    
    public function bulkUpdatePriority($priority)
    {
        $this->executeBulkAction('update_priority', $priority);
    }
    
    public function bulkExport()
    {
        $this->executeBulkAction('export');
    }
    
    protected function executeBulkAction($action, $value = null)
    {
        try {
            $service = app(AdminOrderService::class);
            $count = $service->bulkAction($this->selectedOrders, $action, $value);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Bulk action completed for {$count} orders!"
            ]);
            
            $this->clearSelection();
            $this->loadOrderStats();
            $this->clearOrderCache();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function exportOrders($format = 'csv')
    {
        try {
            $service = app(AdminOrderService::class);
            $fileName = $service->exportOrders($format, [
                'search' => $this->search,
                'status' => $this->statusFilter,
                'priority' => $this->priorityFilter,
                'date_range' => $this->dateRange
            ]);
            
            $this->dispatch('downloadFile', $fileName);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Orders exported successfully!'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ]);
        }
    }
    
    public function generateOrderReport($type = 'comprehensive')
    {
        try {
            $service = app(AdminOrderService::class);
            $reportPath = $service->generateOrderReport($type, $this->dateRange);
            
            $this->dispatch('downloadFile', $reportPath);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Order report generated successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ]);
        }
    }
    
    protected function getOrders()
    {
        $query = Order::with(['user']);
        
        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
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
        
        // Apply priority filter
        if ($this->priorityFilter !== 'all') {
            $query->where('priority', $this->priorityFilter);
        }
        
        // Apply date range filter
        if ($this->dateRange !== 'all') {
            $dates = $this->getDateRangeFilter();
            $query->whereBetween('created_at', [$dates['start'], $dates['end']]);
        }
        
        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        return $query->paginate($this->perPage);
    }
    
    protected function getDateRangeFilter()
    {
        $end = now();
        
        switch ($this->dateRange) {
            case '7_days':
                $start = $end->copy()->subDays(7);
                break;
            case '30_days':
                $start = $end->copy()->subDays(30);
                break;
            case '90_days':
                $start = $end->copy()->subDays(90);
                break;
            case '1_year':
                $start = $end->copy()->subYear();
                break;
            default:
                $start = $end->copy()->subDays(30);
        }
        
        return ['start' => $start, 'end' => $end];
    }
    
    protected function clearOrderCache()
    {
        Cache::forget('order_stats_' . md5($this->statusFilter . $this->dateRange));
        Cache::tags(['orders'])->flush();
    }

    public function render()
    {
        return view('livewire.admin.order-management', [
            'orders' => $this->getOrders()
        ]);
    }
}
