<?php

namespace App\Livewire\Retailer;

use App\Models\Order;
use App\Services\RetailerOrderService;
use Livewire\Component;
use Livewire\WithPagination;

class RetailerOrderManagement extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $searchTerm = '';
    public $dateFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $showOrderCreation = false;

    // Order Detail Modal Properties
    public $showOrderDetails = false;
    public $selectedOrder = null;

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'searchTerm' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = ['orderCreated' => 'handleOrderCreated'];

    public function mount()
    {
        $this->resetPage();
    }

    public function updating($property)
    {
        if (in_array($property, ['statusFilter', 'searchTerm'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->statusFilter = '';
        $this->searchTerm = '';
        $this->resetPage();
    }

    // Handle new order creation
    public function toggleOrderCreation()
    {
        $this->showOrderCreation = !$this->showOrderCreation;
    }

    public function handleOrderCreated($orderId)
    {
        $this->showOrderCreation = false;
        $this->statusFilter = 'pending';
        $this->resetPage();
        session()->flash('message', "Order #{$orderId} created successfully!");
    }

    // Order Detail Modal Methods
    public function showOrderDetails($orderId)
    {
        $this->selectedOrder = Order::with(['orderItems.product', 'buyer'])
            ->where('id', $orderId)
            ->where('buyer_id', auth()->id())
            ->first();

        if ($this->selectedOrder) {
            $this->showOrderDetails = true;
        }
    }

    public function closeOrderDetails()
    {
        $this->showOrderDetails = false;
        $this->selectedOrder = null;
    }

    public function cancelOrder($orderId)
    {
        try {
            $retailerOrderService = app(RetailerOrderService::class);
            $result = $retailerOrderService->cancelOrder($orderId);

            if ($result['success']) {
                $this->closeOrderDetails();
                session()->flash('message', $result['message']);
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $retailerOrderService = app(RetailerOrderService::class);
        $user = auth()->user();

        // Build the query instead of getting paginated results
        $query = Order::where('buyer_id', $user->id);

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply search if needed
        if ($this->searchTerm) {
            $query->where(function ($subQuery) {
                $subQuery->where('id', 'like', '%' . $this->searchTerm . '%');
                // Note: Product search removed as items are stored as JSON
            });
        }

        // Apply sorting and pagination
        $orders = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // Get order statistics
        $orderStats = $retailerOrderService->getOrderStats($user);
        $statusDistribution = $retailerOrderService->getOrderStatusDistribution($user);
        $performanceMetrics = $retailerOrderService->getOrderPerformanceMetrics($user);

        return view('livewire.retailer.retailer-order-management', [
            'orders' => $orders,
            'orderStats' => $orderStats,
            'statusDistribution' => $statusDistribution,
            'performanceMetrics' => $performanceMetrics,
        ]);
    }
}
