<?php

namespace App\Livewire\Sales;

use App\Interfaces\Services\OrderServiceInterface;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $statusFilter = '';
    public $dateFilter = 'all';
    public $startDate = '';
    public $endDate = '';
    public $page = 1;

    protected $queryString = ['searchTerm', 'statusFilter', 'dateFilter'];

    public function mount()
    {
        $this->startDate = Carbon::now()->subMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function resetFilters()
    {
        $this->reset(['searchTerm', 'statusFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function accept($orderId)
    {
        try {
            $orderService = app(OrderServiceInterface::class);
            $result = $orderService->acceptOrder($orderId);

            if ($result) {
                $this->success('Order accepted successfully!');
            } else {
                $this->error('Failed to accept order. Please try again.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function complete($orderId)
    {
        try {
            $orderService = app(OrderServiceInterface::class);
            $result = $orderService->completeOrder($orderId);

            if ($result) {
                $this->success('Order completed successfully!');
            } else {
                $this->error('Failed to complete order. Please try again.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $orderService = app(OrderServiceInterface::class);

        $filters = [];
        if ($this->statusFilter) {
            $filters['status'] = $this->statusFilter;
        }

        // Date filter logic
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        switch ($this->dateFilter) {
            case 'today':
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $startDate = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : Carbon::now()->subMonth()->startOfDay();
                $endDate = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : Carbon::now()->endOfDay();
                break;
            default:
                // 'all' or any other value, fetch without date constraints
                $startDate = Carbon::now()->subYears(10);
                $endDate = Carbon::now()->addYear();
                break;
        }

        // Get orders with applied filters
        $allOrders = $orderService->getOrdersByDateRange($startDate, $endDate, $filters);

        // Apply search if needed
        if ($this->searchTerm) {
            $searchTerm = strtolower($this->searchTerm);
            $allOrders = $allOrders->filter(function ($order) use ($searchTerm) {
                $buyerName = strtolower($order->buyer->name ?? 'unknown');
                $sellerName = strtolower($order->seller->name ?? 'unknown');
                $orderId = (string)$order->id;

                return str_contains($buyerName, $searchTerm) ||
                       str_contains($sellerName, $searchTerm) ||
                       str_contains($orderId, $searchTerm);
            });
        }

        // Get statistics
        $statistics = $orderService->getOrderStatistics($startDate, $endDate);

        // Pagination manually here for better control
        $perPage = 10;
        $currentPage = $this->page;
        $ordersCollection = collect($allOrders);
        $paginatedOrders = new \Illuminate\Pagination\LengthAwarePaginator(
            $ordersCollection->forPage($currentPage, $perPage),
            $ordersCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.sales.order-list', [
            'orders' => $paginatedOrders,
            'statistics' => $statistics,
        ]);
    }
}
