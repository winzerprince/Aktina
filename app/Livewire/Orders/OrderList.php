<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\EnhancedOrderService;
use App\Models\Order;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class OrderList extends Component
{
    use WithPagination;

    public $selectedOrder = null;
    public $showCreateModal = false;
    public $showEditModal = false;
    
    // Order Form Data
    public $buyer_id = '';
    public $seller_id = '';
    public $delivery_address = '';
    public $expected_delivery_date = '';
    public $notes = '';
    public $items = [];
    public $newItem = [
        'resource_id' => '',
        'quantity' => 1,
        'unit_price' => 0
    ];
    
    // Filters
    public $statusFilter = 'all';
    public $userFilter = '';
    public $dateFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $search = '';

    protected $listeners = [
        'orderCreated' => '$refresh',
        'orderUpdated' => '$refresh',
        'refreshOrders' => '$refresh'
    ];

    protected $rules = [
        'buyer_id' => 'required|exists:users,id',
        'seller_id' => 'required|exists:users,id',
        'delivery_address' => 'nullable|string|max:500',
        'expected_delivery_date' => 'nullable|date|after:today',
        'notes' => 'nullable|string|max:1000',
        'items' => 'required|array|min:1',
        'items.*.resource_id' => 'required|exists:resources,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0'
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

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
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

    public function showCreateOrderModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function showEditOrderModal($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Only allow editing pending orders
        if ($order->status !== 'pending') {
            session()->flash('error', 'Only pending orders can be edited.');
            return;
        }
        
        // Load order data into form
        $this->selectedOrder = $order;
        $this->buyer_id = $order->buyer_id;
        $this->seller_id = $order->seller_id;
        $this->delivery_address = $order->delivery_address;
        $this->expected_delivery_date = $order->expected_delivery_date?->format('Y-m-d');
        $this->notes = $order->notes;
        $this->items = $order->items ?? [];
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedOrder = null;
        $this->resetForm();
    }

    public function addItem()
    {
        $this->validate([
            'newItem.resource_id' => 'required|exists:resources,id',
            'newItem.quantity' => 'required|integer|min:1',
            'newItem.unit_price' => 'required|numeric|min:0'
        ]);

        $resource = Resource::find($this->newItem['resource_id']);
        
        $this->items[] = [
            'resource_id' => $this->newItem['resource_id'],
            'resource_name' => $resource->name,
            'quantity' => $this->newItem['quantity'],
            'unit_price' => $this->newItem['unit_price'],
            'total_price' => $this->newItem['quantity'] * $this->newItem['unit_price']
        ];

        $this->newItem = [
            'resource_id' => '',
            'quantity' => 1,
            'unit_price' => 0
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index array
    }

    public function createOrder()
    {
        $this->validate();

        try {
            $orderData = [
                'buyer_id' => $this->buyer_id,
                'seller_id' => $this->seller_id,
                'items' => $this->items,
                'delivery_address' => $this->delivery_address,
                'expected_delivery_date' => $this->expected_delivery_date,
                'notes' => $this->notes
            ];

            $order = $this->orderService->createOrder($orderData);
            
            session()->flash('success', "Order #{$order->id} created successfully.");
            $this->emit('orderCreated');
            $this->closeCreateModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function updateOrder()
    {
        $this->validate();

        try {
            // Update logic would go here
            // For now, we'll just update basic fields
            $this->selectedOrder->update([
                'buyer_id' => $this->buyer_id,
                'seller_id' => $this->seller_id,
                'delivery_address' => $this->delivery_address,
                'expected_delivery_date' => $this->expected_delivery_date,
                'notes' => $this->notes,
                'items' => $this->items
            ]);
            
            session()->flash('success', "Order #{$this->selectedOrder->id} updated successfully.");
            $this->emit('orderUpdated');
            $this->closeEditModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function deleteOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Only allow deletion of pending orders
        if ($order->status !== 'pending') {
            session()->flash('error', 'Only pending orders can be deleted.');
            return;
        }

        try {
            $order->delete();
            session()->flash('success', "Order #{$orderId} deleted successfully.");
            $this->closeOrderModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->buyer_id = '';
        $this->seller_id = '';
        $this->delivery_address = '';
        $this->expected_delivery_date = '';
        $this->notes = '';
        $this->items = [];
        $this->newItem = [
            'resource_id' => '',
            'quantity' => 1,
            'unit_price' => 0
        ];
    }

    public function resetFilters()
    {
        $this->statusFilter = 'all';
        $this->userFilter = '';
        $this->dateFilter = '';
        $this->search = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function getOrdersProperty()
    {
        $user = Auth::user();
        
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
            });

        // Role-based filtering
        if (!$user->hasRole(['admin', 'production_manager'])) {
            $query->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            });
        }

        return $query->orderBy($this->sortBy, $this->sortDirection)->paginate(15);
    }

    public function getUsersProperty()
    {
        return User::select('id', 'name', 'email')->get();
    }

    public function getResourcesProperty()
    {
        return Resource::select('id', 'name', 'category', 'unit_price')->get();
    }

    public function getTotalValueProperty()
    {
        return collect($this->items)->sum('total_price');
    }

    public function render()
    {
        return view('livewire.orders.order-list', [
            'orders' => $this->orders,
            'users' => $this->users,
            'resources' => $this->resources,
            'totalValue' => $this->totalValue,
        ]);
    }
}
