<?php

namespace App\Livewire\Supplier;

use App\Models\Resource;
use App\Services\SupplierService;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierResourceManagement extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $categoryFilter = '';
    public $stockFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'stockFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updating($property)
    {
        if (in_array($property, ['searchTerm', 'categoryFilter', 'stockFilter'])) {
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
        $this->searchTerm = '';
        $this->categoryFilter = '';
        $this->stockFilter = '';
        $this->resetPage();
    }

    public function getStockStatus($quantity)
    {
        if ($quantity <= 0) return 'out_of_stock';
        if ($quantity <= 10) return 'low_stock';
        if ($quantity <= 50) return 'medium_stock';
        return 'high_stock';
    }

    public function render()
    {
        $supplierService = app(SupplierService::class);

        // Build query
        $query = Resource::query();

        // Apply search
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Apply category filter
        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        // Apply stock filter
        if ($this->stockFilter) {
            switch ($this->stockFilter) {
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'low_stock':
                    $query->whereBetween('quantity', [1, 10]);
                    break;
                case 'medium_stock':
                    $query->whereBetween('quantity', [11, 50]);
                    break;
                case 'high_stock':
                    $query->where('quantity', '>', 50);
                    break;
            }
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $resources = $query->paginate(15);

        // Get categories for filter
        $categories = Resource::distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort();

        // Get resource metrics
        $resourceMetrics = $supplierService->getResourceSupplyMetrics();
        $resourceCategories = $supplierService->getResourceCategories();

        return view('livewire.supplier.supplier-resource-management', [
            'resources' => $resources,
            'categories' => $categories,
            'resourceMetrics' => $resourceMetrics,
            'resourceCategories' => $resourceCategories,
        ]);
    }
}
