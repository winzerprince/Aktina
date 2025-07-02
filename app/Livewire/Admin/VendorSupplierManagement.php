<?php

namespace App\Livewire\Admin;

use App\Services\VendorManagementService;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class VendorSupplierManagement extends Component
{
    use WithPagination;

    public $vendors = [];
    public $suppliers = [];
    public $vendorStats = [];
    public $supplierStats = [];
    public $selectedType = 'vendors';
    public $selectedVendor = null;
    public $showVendorModal = false;
    public $vendorDetails = [];
    public $searchTerm = '';
    public $filterStatus = 'all';
    public $filterPerformance = 'all';

    public $typeOptions = [
        'vendors' => 'Vendors',
        'suppliers' => 'Suppliers'
    ];

    public $statusFilters = [
        'all' => 'All Status',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended'
    ];

    public $performanceFilters = [
        'all' => 'All Performance',
        'excellent' => 'Excellent (90%+)',
        'good' => 'Good (70-89%)',
        'average' => 'Average (50-69%)',
        'poor' => 'Poor (<50%)'
    ];

    protected $vendorManagementService;

    public function boot(VendorManagementService $vendorManagementService)
    {
        $this->vendorManagementService = $vendorManagementService;
    }

    public function mount()
    {
        $this->loadData();
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function updatedFilterPerformance()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function loadData()
    {
        $filters = [
            'search' => $this->searchTerm,
            'status' => $this->filterStatus !== 'all' ? $this->filterStatus : null,
            'performance' => $this->filterPerformance !== 'all' ? $this->filterPerformance : null,
        ];

        if ($this->selectedType === 'vendors') {
            $this->vendors = $this->vendorManagementService->getVendors($filters, 20);
            $this->vendorStats = $this->vendorManagementService->getVendorStatistics();
        } else {
            $this->suppliers = $this->vendorManagementService->getSuppliers($filters, 20);
            $this->supplierStats = $this->vendorManagementService->getSupplierStatistics();
        }
    }

    public function showVendorDetails($vendorId)
    {
        $this->selectedVendor = $vendorId;
        $this->vendorDetails = $this->vendorManagementService->getVendorDetails($vendorId);
        $this->showVendorModal = true;
    }

    public function closeVendorModal()
    {
        $this->showVendorModal = false;
        $this->selectedVendor = null;
        $this->vendorDetails = [];
    }

    public function updateVendorStatus($vendorId, $status)
    {
        try {
            $this->vendorManagementService->updateVendorStatus($vendorId, $status);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => ucfirst($this->selectedType) . ' status updated successfully'
            ]);

            $this->loadData();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Status update failed: ' . $e->getMessage()
            ]);
        }
    }

    public function sendMessage($vendorId)
    {
        // Redirect to communication interface
        $this->dispatch('navigate-to-chat', ['userId' => $vendorId]);
    }

    public function exportVendors()
    {
        try {
            $filters = [
                'type' => $this->selectedType,
                'status' => $this->filterStatus !== 'all' ? $this->filterStatus : null,
                'performance' => $this->filterPerformance !== 'all' ? $this->filterPerformance : null,
            ];

            $this->vendorManagementService->exportVendors($filters);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Export started. Download will begin shortly.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ]);
        }
    }

    public function getPerformanceColor($rating)
    {
        if ($rating >= 90) return 'green';
        if ($rating >= 70) return 'blue';
        if ($rating >= 50) return 'yellow';
        return 'red';
    }

    public function getPerformanceLabel($rating)
    {
        if ($rating >= 90) return 'Excellent';
        if ($rating >= 70) return 'Good';
        if ($rating >= 50) return 'Average';
        return 'Poor';
    }

    #[On('refresh-vendors')]
    public function refreshVendors()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.vendor-supplier-management');
    }
}
