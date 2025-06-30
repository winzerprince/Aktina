<?php

namespace App\Livewire\Admin;

use App\Models\Application;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $query = Application::with(['vendor.user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('application_reference', 'like', '%' . $this->search . '%')
                      ->orWhereHas('vendor.user', function ($subQ) {
                          $subQ->where('name', 'like', '%' . $this->search . '%')
                               ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $applications = $query->paginate(10);

        return view('livewire.admin.applications-table', [
            'applications' => $applications,
            'statusOptions' => [
                'pending' => 'Pending',
                'scored' => 'Scored',
                'meeting_scheduled' => 'Meeting Scheduled',
                'meeting_completed' => 'Meeting Completed',
                'approved' => 'Approved',
                'rejected' => 'Rejected'
            ]
        ]);
    }
}
