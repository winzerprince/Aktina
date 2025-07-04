<?php

namespace App\Livewire\ProductionManager\Inventory;

use App\Models\Product;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $selectedWarehouse = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $categories = [];
    public $warehouses = [];

    protected $inventoryService;

    public function boot(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function mount()
    {
        $this->loadFilters();
    }

    public function loadFilters()
    {
        $this->categories = $this->inventoryService->getProductCategories();
        $this->warehouses = $this->inventoryService->getWarehouses();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedSelectedWarehouse()
    {
        $this->resetPage();
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

    public function getProductsProperty()
    {
        return Product::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category', $this->selectedCategory);
            })
            ->when($this->selectedWarehouse, function ($query) {
                $query->whereHas('bom.resources', function ($q) {
                    $q->where('warehouse_id', $this->selectedWarehouse);
                });
            })
            ->with(['bom.resources.warehouse'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.production-manager.inventory.products-table', [
            'products' => $this->products
        ]);
    }
}
