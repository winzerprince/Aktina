<?php

namespace App\Livewire\HRManager;

use App\Models\User;
use App\Services\HRService;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeManagement extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updating($property)
    {
        if (in_array($property, ['searchTerm', 'roleFilter', 'statusFilter'])) {
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
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function getEmployeeStatus($user)
    {
        if ($user->email_verified_at) {
            return 'active';
        }
        return 'inactive';
    }

    public function render()
    {
        $hrService = app(HRService::class);

        // Build query
        $query = User::query();

        // Apply search
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('company_name', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Apply role filter
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        // Apply status filter
        if ($this->statusFilter) {
            if ($this->statusFilter === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $employees = $query->paginate(15);

        // Get roles for filter
        $roles = User::distinct()
            ->whereNotNull('role')
            ->pluck('role')
            ->sort();

        // Get employee performance metrics
        $performanceMetrics = $hrService->getEmployeePerformanceMetrics();
        $performanceMap = collect($performanceMetrics)->keyBy('user_id');

        // Get employee stats
        $employeeStats = $hrService->getEmployeeStats();

        return view('livewire.hr-manager.employee-management', [
            'employees' => $employees,
            'roles' => $roles,
            'employeeStats' => $employeeStats,
            'performanceMap' => $performanceMap,
        ]);
    }
}
