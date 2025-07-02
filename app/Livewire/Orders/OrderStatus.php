<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\EnhancedOrderService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderStatus extends Component
{
    use WithPagination;

    public $selectedOrder = null;
    public $showUpdateModal = false;
    public $newStatus = '';
    public $statusNotes = '';
    
    // Filters
    public $statusFilter = 'all';
    public $userFilter = '';
    public $dateRangeFilter = '';
    public $search = '';

    protected $listeners = [
        'statusUpdated' => '$refresh',
        'refreshOrders' => '$refresh'
    ];

    public function __construct(
        private EnhancedOrderService $orderService
    ) {}

    public function boot(EnhancedOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with([
            'buyer', 
            'seller', 
            'approver', 
            'assignedWarehouse'
        ])->findOrFail($orderId);
    }

    public function closeOrderModal()
    {
        $this->selectedOrder = null;
    }

    public function showUpdateStatusModal($orderId)
    {
        $this->selectedOrder = Order::findOrFail($orderId);
        $this->newStatus = $this->selectedOrder->status;
        $this->statusNotes = '';
        $this->showUpdateModal = true;
    }

    public function closeUpdateModal()
    {
        $this->showUpdateModal = false;
        $this->selectedOrder = null;
        $this->newStatus = '';
        $this->statusNotes = '';
    }

    public function updateOrderStatus()
    {
        $this->validate([
            'newStatus' => 'required|string|in:pending,accepted,in_fulfillment,shipped,completed,rejected,cancelled',
            'statusNotes' => 'nullable|string|max:500'
        ]);

        try {
            $order = $this->orderService->updateOrderStatus(
                $this->selectedOrder->id, 
                $this->newStatus
            );
            
            // Add notes if provided
            if ($this->statusNotes) {
                $order->update(['notes' => $this->statusNotes]);
            }
            
            session()->flash('success', "Order #{$order->id} status updated to " . ucfirst($this->newStatus));
            $this->emit('statusUpdated');
            $this->closeUpdateModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    public function trackOrder($orderId)
    {
        $this->viewOrder($orderId);
        // Additional tracking logic can be added here
    }

    public function resetFilters()
    {
        $this->statusFilter = 'all';
        $this->userFilter = '';
        $this->dateRangeFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function getOrdersProperty()
    {
        $user = Auth::user();
        
        $query = Order::with(['buyer', 'seller', 'approver', 'assignedWarehouse'])
            ->when($this->statusFilter && $this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('id', 'like', '%' . $this->search . '%')
                          ->orWhereHas('buyer', function ($userQuery) {
                              $userQuery->where('name', 'like', '%' . $this->search . '%');
                          })
                          ->orWhereHas('seller', function ($userQuery) {
                              $userQuery->where('name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->when($this->userFilter, function ($q) {
                $q->where(function ($query) {
                    $query->where('buyer_id', $this->userFilter)
                          ->orWhere('seller_id', $this->userFilter);
                });
            })
            ->when($this->dateRangeFilter, function ($q) {
                switch ($this->dateRangeFilter) {
                    case 'today':
                        $q->whereDate('created_at', today());
                        break;
                    case 'week':
                        $q->where('created_at', '>=', now()->subWeek());
                        break;
                    case 'month':
                        $q->where('created_at', '>=', now()->subMonth());
                        break;
                }
            });

        // Role-based filtering
        if (!$user->hasRole(['admin', 'production_manager'])) {
            $query->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function getStatusStatsProperty()
    {
        $user = Auth::user();
        $baseQuery = Order::query();
        
        if (!$user->hasRole(['admin', 'production_manager'])) {
            $baseQuery->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            });
        }

        return [
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'accepted' => (clone $baseQuery)->where('status', 'accepted')->count(),
            'in_fulfillment' => (clone $baseQuery)->where('status', 'in_fulfillment')->count(),
            'shipped' => (clone $baseQuery)->where('status', 'shipped')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];
    }

    public function getOrderStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'in_fulfillment' => 'In Fulfillment',
            'shipped' => 'Shipped',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled'
        ];
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'yellow',
            'accepted' => 'blue',
            'in_fulfillment' => 'indigo',
            'shipped' => 'purple',
            'completed' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function render()
    {
        return view('livewire.orders.order-status', [
            'orders' => $this->orders,
            'statusStats' => $this->statusStats,
            'statusOptions' => $this->getOrderStatusOptions(),
        ]);
    }
}
