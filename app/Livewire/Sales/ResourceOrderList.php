<?php

namespace App\Livewire\Sales;

use App\Interfaces\Services\ResourceOrderServiceInterface;
use App\Models\ResourceOrder;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceOrderList extends Component
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
        // Check if user is from Aktina company
        if (!auth()->user() || auth()->user()->company_name !== 'Aktina') {
            abort(403, 'Access denied. This section is only available to Aktina employees.');
        }

        $this->startDate = Carbon::now()->subMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function resetFilters()
    {
        $this->reset(['searchTerm', 'statusFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function accept($resourceOrderId)
    {
        try {
            $resourceOrderService = app(ResourceOrderServiceInterface::class);
            $result = $resourceOrderService->acceptResourceOrder($resourceOrderId);

            if ($result) {
                session()->flash('success', 'Resource order accepted successfully!');
            } else {
                session()->flash('error', 'Failed to accept resource order. Please try again.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function complete($resourceOrderId)
    {
        try {
            $resourceOrderService = app(ResourceOrderServiceInterface::class);
            $result = $resourceOrderService->completeResourceOrder($resourceOrderId);

            if ($result) {
                $this->success('Resource order completed successfully!');
            } else {
                $this->error('Failed to complete resource order. Please try again.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $resourceOrderService = app(ResourceOrderServiceInterface::class);

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

        // Get resource orders with applied filters
        $allResourceOrders = $resourceOrderService->getResourceOrdersByDateRange($startDate, $endDate, $filters);

        // Apply search if needed
        if ($this->searchTerm) {
            $searchTerm = strtolower($this->searchTerm);
            $allResourceOrders = $allResourceOrders->filter(function ($resourceOrder) use ($searchTerm) {
                $buyerName = strtolower($resourceOrder->buyer->name ?? 'unknown');
                $sellerName = strtolower($resourceOrder->seller->name ?? 'unknown');
                $resourceOrderId = (string)$resourceOrder->id;

                return str_contains($buyerName, $searchTerm) ||
                       str_contains($sellerName, $searchTerm) ||
                       str_contains($resourceOrderId, $searchTerm);
            });
        }

        // Get statistics
        $statistics = $resourceOrderService->getResourceOrderStatistics($startDate, $endDate);

        // Pagination manually for better control
        $perPage = 10;
        $currentPage = $this->page;
        $resourceOrdersCollection = collect($allResourceOrders);
        $paginatedResourceOrders = new \Illuminate\Pagination\LengthAwarePaginator(
            $resourceOrdersCollection->forPage($currentPage, $perPage),
            $resourceOrdersCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.sales.resource-order-list', [
            'resourceOrders' => $paginatedResourceOrders,
            'statistics' => $statistics,
        ]);
    }
}
