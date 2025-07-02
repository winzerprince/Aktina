<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\EnhancedOrderService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderApproval extends Component
{
    use WithPagination;

    public $selectedOrder = null;
    public $rejectionReason = '';
    public $showRejectionModal = false;
    public $orderToReject = null;
    
    // Filters
    public $statusFilter = 'pending';
    public $userFilter = '';
    public $dateFilter = '';
    public $valueFilter = '';
    public $search = '';

    protected $listeners = [
        'orderApproved' => '$refresh',
        'orderRejected' => '$refresh',
        'refreshOrders' => '$refresh'
    ];

    public function __construct(
        private EnhancedOrderService $orderService
    ) {}

    public function boot(EnhancedOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function mount()
    {
        // Only accessible to users with approval permissions
        if (!Auth::user()->hasRole(['admin', 'production_manager'])) {
            abort(403, 'Unauthorized access to order approval.');
        }
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
        $this->selectedOrder = Order::with(['buyer', 'seller', 'approver', 'assignedWarehouse'])
            ->findOrFail($orderId);
    }

    public function closeOrderModal()
    {
        $this->selectedOrder = null;
    }

    public function approveOrder($orderId)
    {
        try {
            $order = $this->orderService->approveOrder($orderId, Auth::id());
            
            session()->flash('success', "Order #{$order->id} has been approved successfully.");
            $this->emit('orderApproved');
            $this->closeOrderModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve order: ' . $e->getMessage());
        }
    }

    public function showRejectModal($orderId)
    {
        $this->orderToReject = $orderId;
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    public function closeRejectModal()
    {
        $this->orderToReject = null;
        $this->rejectionReason = '';
        $this->showRejectionModal = false;
    }

    public function rejectOrder()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10|max:500'
        ]);

        try {
            $order = $this->orderService->rejectOrder(
                $this->orderToReject, 
                Auth::id(), 
                $this->rejectionReason
            );
            
            session()->flash('success', "Order #{$order->id} has been rejected.");
            $this->emit('orderRejected');
            $this->closeRejectModal();
            $this->closeOrderModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject order: ' . $e->getMessage());
        }
    }

    public function resetFilters()
    {
        $this->statusFilter = 'pending';
        $this->userFilter = '';
        $this->dateFilter = '';
        $this->valueFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function getOrdersProperty()
    {
        $query = Order::with(['buyer', 'seller', 'approver'])
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
            ->when($this->dateFilter, function ($q) {
                $date = \Carbon\Carbon::parse($this->dateFilter);
                $q->whereDate('created_at', $date);
            })
            ->when($this->valueFilter, function ($q) {
                switch ($this->valueFilter) {
                    case 'low':
                        $q->where('price', '<', 1000);
                        break;
                    case 'medium':
                        $q->whereBetween('price', [1000, 10000]);
                        break;
                    case 'high':
                        $q->where('price', '>', 10000);
                        break;
                }
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate(10);
    }

    public function getUsersProperty()
    {
        return User::select('id', 'name')->get();
    }

    public function getPendingCountProperty()
    {
        return Order::where('status', 'pending')->count();
    }

    public function getApprovedTodayProperty()
    {
        return Order::where('status', 'accepted')
            ->whereDate('approved_at', today())
            ->count();
    }

    public function getTotalValuePendingProperty()
    {
        return Order::where('status', 'pending')->sum('price');
    }

    public function render()
    {
        return view('livewire.orders.order-approval', [
            'orders' => $this->orders,
            'users' => $this->users,
            'pendingCount' => $this->pendingCount,
            'approvedToday' => $this->approvedToday,
            'totalValuePending' => $this->totalValuePending,
        ]);
    }
}
