<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Interfaces\Services\VendorApplicationServiceInterface;
use Illuminate\Support\Carbon;

class VendorApplicationsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Meeting scheduling
    public $showMeetingModal = false;
    public $selectedApplicationId = null;
    public $meetingDate = '';
    public $meetingTime = '';

    private VendorApplicationServiceInterface $vendorApplicationService;

    public function boot(VendorApplicationServiceInterface $vendorApplicationService)
    {
        $this->vendorApplicationService = $vendorApplicationService;
    }

    protected $listeners = ['refreshTable' => '$refresh'];

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
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function changeStatus($applicationId, $newStatus)
    {
        try {
            $this->vendorApplicationService->changeApplicationStatus($applicationId, $newStatus);
            session()->flash('message', 'Application status updated successfully.');
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update application status: ' . $e->getMessage());
        }
    }

    public function openMeetingModal($applicationId)
    {
        $this->selectedApplicationId = $applicationId;
        $this->meetingDate = '';
        $this->meetingTime = '';
        $this->showMeetingModal = true;
    }    public function scheduleMeeting()
    {
        $this->validate([
            'meetingDate' => 'required|date|after:today',
            'meetingTime' => 'required',
        ]);

        try {
            $meetingDateTime = Carbon::parse($this->meetingDate . ' ' . $this->meetingTime);
            $this->vendorApplicationService->scheduleMeeting($this->selectedApplicationId, $meetingDateTime);

            $this->showMeetingModal = false;
            session()->flash('message', 'Meeting scheduled successfully.');
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to schedule meeting: ' . $e->getMessage());
        }
    }

    public function triggerPdfProcessing($applicationId)
    {
        try {
            $this->vendorApplicationService->triggerPdfProcessing($applicationId);
            session()->flash('message', 'PDF processing triggered successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to trigger PDF processing: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $applications = $this->vendorApplicationService->getApplications([
            'search' => $this->search,
            'status_filter' => $this->statusFilter,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection,
            'per_page' => 10
        ]);

        return view('livewire.admin.vendor-applications-table', [
            'applications' => $applications
        ]);
    }
}
