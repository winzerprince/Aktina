<?php

namespace App\Livewire\Admin\Sales;

use App\Interfaces\Repositories\SalesRepositoryInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class Table extends Component
{
    use WithPagination;

    public int $perPage = 10;

    protected SalesRepositoryInterface $salesRepository;

    public function boot(SalesRepositoryInterface $salesRepository): void
    {
        $this->salesRepository = $salesRepository;
    }

    public function mount(): void
    {
        // No date filtering - show all sales ever
    }

    public function render()
    {
        try {
            // Get all orders from production managers (no date filtering)
            $allOrders = $this->salesRepository->getAllProductionManagerOrders();

            // Create manual pagination
            $currentPage = $this->getPage();
            $offset = ($currentPage - 1) * $this->perPage;
            $itemsForCurrentPage = $allOrders->slice($offset, $this->perPage)->values();

            $paginatedOrders = new LengthAwarePaginator(
                $itemsForCurrentPage,
                $allOrders->count(),
                $this->perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );

            return view('livewire.admin.sales.table', [
                'orders' => $paginatedOrders
            ]);
        } catch (\Exception $e) {
            \Log::error('Sales Table Error:', ['error' => $e->getMessage()]);

            // Return empty paginated result
            $emptyPagination = new LengthAwarePaginator(
                collect(),
                0,
                $this->perPage,
                1,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );

            return view('livewire.admin.sales.table', [
                'orders' => $emptyPagination
            ]);
        }
    }
}
