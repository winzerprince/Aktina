<?php

namespace App\Livewire\Sales;

use App\Models\Order;
use App\Models\Product;
use App\Services\SalesService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class SalesTable extends Component
{
    use WithPagination;

    public string $companyName;
    public string $startDate;
    public string $endDate;
    public bool $showOrderModal = false;
    public $selectedOrder = null;
    public int $perPage = 10; // Add pagination control

    public function mount()
    {
        $user = auth()->user();

        // For admin users, use a special identifier to get all sales
        // For regular users, use their company name or fallback to empty string
        if ($user->role === 'admin') {
            $this->companyName = '*'; // Special identifier for all companies
        } else {
            $this->companyName = $user->company_name ?? 'No Company';
        }

        $this->startDate = Carbon::now()->subDays(29)->toDateString();
        $this->endDate = Carbon::now()->toDateString();
    }

    public function viewOrder($orderId)
    {
        // Fetch the specific order with complete buyer relationship
        $this->selectedOrder = Order::with(['buyer:id,name,email,company_name,role', 'seller:id,name,email,company_name'])
            ->find($orderId);

        $this->showOrderModal = true;
    }

    public function exportOrder($orderId)
    {
        // Handle export order action
        $this->dispatch('export-order', ['orderId' => $orderId]);
    }

    public function closeModal()
    {
        $this->showOrderModal = false;
        $this->selectedOrder = null;
    }

    public function getProductDetails($productId)
    {
        return Product::find($productId);
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function render(SalesService $salesService)
    {
        $sales = $salesService->getSalesForTable(
            $this->companyName,
            $this->startDate,
            $this->endDate,
            $this->perPage
        );

        return view('livewire.sales.sales-table', [
            'sales' => $sales,
        ]);
    }
}
