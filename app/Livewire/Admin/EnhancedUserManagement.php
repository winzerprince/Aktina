<?php

namespace App\Livewire\Admin;

use App\Services\UserManagementService;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class EnhancedUserManagement extends Component
{
    use WithPagination;

    public $users = [];
    public $selectedUsers = [];
    public $bulkAction = '';
    public $searchTerm = '';
    public $filterRole = 'all';
    public $filterStatus = 'all';
    public $showUserModal = false;
    public $editingUser = null;
    public $userForm = [
        'name' => '',
        'email' => '',
        'role' => '',
        'is_active' => true,
        'company_name' => '',
        'phone' => '',
    ];

    public $roles = [
        'admin' => 'Administrator',
        'production_manager' => 'Production Manager',
        'supplier' => 'Supplier',
        'vendor' => 'Vendor',
        'retailer' => 'Retailer',
        'hr_manager' => 'HR Manager'
    ];

    public $bulkActions = [
        '' => 'Select Action',
        'activate' => 'Activate Users',
        'deactivate' => 'Deactivate Users',
        'delete' => 'Delete Users',
        'export' => 'Export Selected',
        'send_notification' => 'Send Notification'
    ];

    protected $userManagementService;

    public function boot(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    public function mount()
    {
        $this->loadUsers();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->loadUsers();
    }

    public function updatedFilterRole()
    {
        $this->resetPage();
        $this->loadUsers();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $filters = [
            'search' => $this->searchTerm,
            'role' => $this->filterRole !== 'all' ? $this->filterRole : null,
            'status' => $this->filterStatus !== 'all' ? ($this->filterStatus === 'active') : null,
        ];

        $this->users = $this->userManagementService->getFilteredUsers($filters, 20);
    }

    public function toggleUserSelection($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_filter($this->selectedUsers, fn($id) => $id !== $userId);
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    public function selectAllUsers()
    {
        $this->selectedUsers = $this->users->pluck('id')->toArray();
    }

    public function clearSelection()
    {
        $this->selectedUsers = [];
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedUsers) || empty($this->bulkAction)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Please select users and an action'
            ]);
            return;
        }

        try {
            $result = $this->userManagementService->executeBulkAction(
                $this->bulkAction,
                $this->selectedUsers
            );

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => $result['message']
            ]);

            $this->clearSelection();
            $this->bulkAction = '';
            $this->loadUsers();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Action failed: ' . $e->getMessage()
            ]);
        }
    }

    public function createUser()
    {
        $this->resetUserForm();
        $this->editingUser = null;
        $this->showUserModal = true;
    }

    public function editUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $this->editingUser = $user;
            $this->userForm = [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'company_name' => $user->company_name,
                'phone' => $user->phone,
            ];
            $this->showUserModal = true;
        }
    }

    public function saveUser()
    {
        $this->validate([
            'userForm.name' => 'required|string|max:255',
            'userForm.email' => 'required|email|max:255',
            'userForm.role' => 'required|in:' . implode(',', array_keys($this->roles)),
            'userForm.company_name' => 'nullable|string|max:255',
            'userForm.phone' => 'nullable|string|max:20',
        ]);

        try {
            if ($this->editingUser) {
                $this->userManagementService->updateUser($this->editingUser->id, $this->userForm);
                $message = 'User updated successfully';
            } else {
                $this->userManagementService->createUser($this->userForm);
                $message = 'User created successfully';
            }

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => $message
            ]);

            $this->closeUserModal();
            $this->loadUsers();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Operation failed: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteUser($userId)
    {
        try {
            $this->userManagementService->deleteUser($userId);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'User deleted successfully'
            ]);

            $this->loadUsers();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Delete failed: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleUserStatus($userId)
    {
        try {
            $this->userManagementService->toggleUserStatus($userId);
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'User status updated successfully'
            ]);

            $this->loadUsers();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Status update failed: ' . $e->getMessage()
            ]);
        }
    }

    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->editingUser = null;
        $this->resetUserForm();
    }

    private function resetUserForm()
    {
        $this->userForm = [
            'name' => '',
            'email' => '',
            'role' => '',
            'is_active' => true,
            'company_name' => '',
            'phone' => '',
        ];
    }

    public function exportUsers()
    {
        try {
            $filters = [
                'search' => $this->searchTerm,
                'role' => $this->filterRole !== 'all' ? $this->filterRole : null,
                'status' => $this->filterStatus !== 'all' ? ($this->filterStatus === 'active') : null,
            ];

            $this->userManagementService->exportUsers($filters);

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

    #[On('refresh-users')]
    public function refreshUsers()
    {
        $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.admin.enhanced-user-management');
    }
}
