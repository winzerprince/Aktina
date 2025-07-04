<?php

namespace App\Livewire\ProductionManager\Inventory;

use App\Models\Resource;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class ResourcesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedType = '';
    public $selectedWarehouse = '';
    public $selectedStatus = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $resourceTypes = [];
    public $warehouses = [];
    public $statusOptions = [
        'available' => 'Available',
        'reserved' => 'Reserved',
        'maintenance' => 'Under Maintenance',
        'low_stock' => 'Low Stock',
        'out_of_stock' => 'Out of Stock'
    ];

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
        $this->resourceTypes = $this->inventoryService->getResourceTypes();
        $this->warehouses = $this->inventoryService->getWarehouses();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
    }

    public function updatedSelectedWarehouse()
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus()
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

    public function getResourcesProperty()
    {
        return Resource::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('part_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedType, function ($query) {
                $query->where('component_type', $this->selectedType);
            })
            ->when($this->selectedWarehouse, function ($query) {
                $query->where('warehouse_id', $this->selectedWarehouse);
            })
            ->when($this->selectedStatus, function ($query) {
                if ($this->selectedStatus === 'low_stock') {
                    $query->whereRaw('available_quantity <= reorder_level');
                } elseif ($this->selectedStatus === 'out_of_stock') {
                    $query->where('units', 0);
                } elseif ($this->selectedStatus === 'available') {
                    $query->where('units', '>', 0);
                }
            })
            ->with(['warehouse'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function requestResource($resourceId, $quantity)
    {
        try {
            $this->inventoryService->requestResource($resourceId, $quantity);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Resource request submitted successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to request resource: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.production-manager.inventory.resources-table', [
            'resources' => $this->resources
        ]);
    }
}
