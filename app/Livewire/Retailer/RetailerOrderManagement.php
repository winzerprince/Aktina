<?php

namespace App\Livewire\Retailer;

use App\Services\RetailerOrderService;
use Livewire\Component;
use Livewire\WithPagination;

class RetailerOrderManagement extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $searchTerm = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'searchTerm' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

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

    public function render()
    {
        $retailerOrderService = app(RetailerOrderService::class);
        $user = auth()->user();

        // Get orders with pagination
        $orders = $retailerOrderService->getOrdersByStatus($user, $this->statusFilter ?: null);

        // Apply search if needed
        if ($this->searchTerm) {
            $orders->where(function ($query) {
                $query->where('id', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('orderItems.product', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        // Apply sorting
        $orders->orderBy($this->sortField, $this->sortDirection);

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
