<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Interfaces\Services\UserManagementServiceInterface;

class UserManagementTable extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $verificationFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    private UserManagementServiceInterface $userManagementService;

    public function boot(UserManagementServiceInterface $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    protected $listeners = ['refreshTable' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingVerificationFilter()
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

    public function verifyUser($userId)
    {
        try {
            $this->userManagementService->verifyUser($userId);
            session()->flash('message', 'User verified successfully.');
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to verify user: ' . $e->getMessage());
        }
    }

    public function unverifyUser($userId)
    {
        try {
            $this->userManagementService->unverifyUser($userId);
            session()->flash('message', 'User unverified successfully.');
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to unverify user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $users = $this->userManagementService->getUsers([
            'search' => $this->search,
            'role_filter' => $this->roleFilter,
            'verification_filter' => $this->verificationFilter,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection,
            'per_page' => 10
        ]);

        return view('livewire.admin.user-management-table', [
            'users' => $users
        ]);
    }
}
