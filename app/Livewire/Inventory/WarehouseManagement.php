<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Interfaces\Services\WarehouseServiceInterface;
use App\Models\Warehouse;

class WarehouseManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedWarehouse = null;
    
    // Form fields
    public $name = '';
    public $location = '';
    public $capacity = '';
    public $current_usage = '';
    public $manager_name = '';
    public $contact_email = '';
    public $contact_phone = '';

    protected $warehouseService;

    protected $rules = [
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'capacity' => 'required|numeric|min:0',
        'current_usage' => 'nullable|numeric|min:0',
        'manager_name' => 'nullable|string|max:255',
        'contact_email' => 'nullable|email|max:255',
        'contact_phone' => 'nullable|string|max:20'
    ];

    public function boot(WarehouseServiceInterface $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function updatingSearch()
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

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($warehouseId)
    {
        $warehouse = Warehouse::findOrFail($warehouseId);
        $this->selectedWarehouse = $warehouse;
        
        $this->name = $warehouse->name;
        $this->location = $warehouse->location;
        $this->capacity = $warehouse->capacity;
        $this->current_usage = $warehouse->current_usage;
        $this->manager_name = $warehouse->manager_name;
        $this->contact_email = $warehouse->contact_email;
        $this->contact_phone = $warehouse->contact_phone;
        
        $this->showEditModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function createWarehouse()
    {
        $this->validate();

        try {
            $this->warehouseService->createWarehouse([
                'name' => $this->name,
                'location' => $this->location,
                'capacity' => $this->capacity,
                'current_usage' => $this->current_usage ?? 0,
                'manager_name' => $this->manager_name,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
            ]);

            session()->flash('success', 'Warehouse created successfully!');
            $this->closeModals();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create warehouse: ' . $e->getMessage());
        }
    }

    public function updateWarehouse()
    {
        $this->validate();

        try {
            $this->warehouseService->updateWarehouse($this->selectedWarehouse->id, [
                'name' => $this->name,
                'location' => $this->location,
                'capacity' => $this->capacity,
                'current_usage' => $this->current_usage ?? 0,
                'manager_name' => $this->manager_name,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
            ]);

            session()->flash('success', 'Warehouse updated successfully!');
            $this->closeModals();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update warehouse: ' . $e->getMessage());
        }
    }

    public function deleteWarehouse($warehouseId)
    {
        try {
            $this->warehouseService->deleteWarehouse($warehouseId);
            session()->flash('success', 'Warehouse deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete warehouse: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->name = '';
        $this->location = '';
        $this->capacity = '';
        $this->current_usage = '';
        $this->manager_name = '';
        $this->contact_email = '';
        $this->contact_phone = '';
        $this->selectedWarehouse = null;
    }

    public function render()
    {
        $warehouses = Warehouse::when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('location', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.inventory.warehouse-management', [
            'warehouses' => $warehouses
        ]);
    }
}
