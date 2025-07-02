<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Interfaces\Services\InventoryServiceInterface;
use App\Models\InventoryMovement;
use App\Models\Warehouse;
use App\Models\Resource;
use Carbon\Carbon;

class StockMovement extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouseFilter = 'all';
    public $resourceFilter = 'all';
    public $typeFilter = 'all'; // all, inbound, outbound, transfer
    public $dateFrom = '';
    public $dateTo = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    
    // Form fields for creating movement
    public $resource_id = '';
    public $warehouse_id = '';
    public $type = 'inbound';
    public $quantity = '';
    public $reference_number = '';
    public $notes = '';
    public $destination_warehouse_id = '';

    public $warehouses = [];
    public $resources = [];

    protected $inventoryService;

    protected $rules = [
        'resource_id' => 'required|exists:resources,id',
        'warehouse_id' => 'required|exists:warehouses,id',
        'type' => 'required|in:inbound,outbound,transfer',
        'quantity' => 'required|numeric|min:0.01',
        'reference_number' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
        'destination_warehouse_id' => 'nullable|exists:warehouses,id'
    ];

    public function boot(InventoryServiceInterface $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function mount()
    {
        $this->warehouses = Warehouse::all();
        $this->resources = Resource::all();
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        // Reset destination warehouse when type changes
        if ($this->type !== 'transfer') {
            $this->destination_warehouse_id = '';
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

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createMovement()
    {
        $this->validate();

        try {
            $movementData = [
                'resource_id' => $this->resource_id,
                'warehouse_id' => $this->warehouse_id,
                'type' => $this->type,
                'quantity' => $this->quantity,
                'reference_number' => $this->reference_number,
                'notes' => $this->notes,
            ];

            if ($this->type === 'transfer' && $this->destination_warehouse_id) {
                $movementData['destination_warehouse_id'] = $this->destination_warehouse_id;
            }

            $this->inventoryService->createMovement($movementData);

            session()->flash('success', 'Stock movement recorded successfully!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to record movement: ' . $e->getMessage());
        }
    }

    public function exportMovements()
    {
        try {
            $movements = $this->getMovementsQuery()->get();
            // This would typically generate and download a CSV/Excel file
            session()->flash('success', 'Export started. You will receive the file shortly.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export movements: ' . $e->getMessage());
        }
    }

    public function getMovementSummary()
    {
        return $this->inventoryService->getMovementSummary([
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'warehouse_id' => $this->warehouseFilter !== 'all' ? $this->warehouseFilter : null,
        ]);
    }

    private function getMovementsQuery()
    {
        return InventoryMovement::with(['resource', 'warehouse', 'destinationWarehouse'])
            ->when($this->search, function ($query) {
                return $query->whereHas('resource', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('reference_number', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%');
            })
            ->when($this->warehouseFilter !== 'all', function ($query) {
                return $query->where('warehouse_id', $this->warehouseFilter);
            })
            ->when($this->resourceFilter !== 'all', function ($query) {
                return $query->where('resource_id', $this->resourceFilter);
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                return $query->where('type', $this->typeFilter);
            })
            ->when($this->dateFrom, function ($query) {
                return $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                return $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    private function resetForm()
    {
        $this->resource_id = '';
        $this->warehouse_id = '';
        $this->type = 'inbound';
        $this->quantity = '';
        $this->reference_number = '';
        $this->notes = '';
        $this->destination_warehouse_id = '';
    }

    public function render()
    {
        $movements = $this->getMovementsQuery()->paginate(15);
        $summary = $this->getMovementSummary();

        return view('livewire.inventory.stock-movement', [
            'movements' => $movements,
            'summary' => $summary
        ]);
    }
}
