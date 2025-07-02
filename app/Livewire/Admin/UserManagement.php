<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Support\Facades\Cache;

class UserManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $roleFilter = 'all';
    public $statusFilter = 'all';
    public $perPage = 15;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Modal states
    public $showUserModal = false;
    public $showBulkActions = false;
    public $selectedUsers = [];
    
    // User form data
    public $userForm = [
        'id' => null,
        'name' => '',
        'email' => '',
        'role' => 'user',
        'status' => 'active'
    ];
    
    // Statistics
    public $userStats = [];
    
    protected $listeners = [
        'refreshUsers' => 'loadUserStats',
        'userUpdated' => 'refreshUsers',
        'bulkActionCompleted' => 'refreshUsers'
    ];
    
    protected $rules = [
        'userForm.name' => 'required|min:2|max:255',
        'userForm.email' => 'required|email|max:255',
        'userForm.role' => 'required|in:admin,manager,user,vendor',
        'userForm.status' => 'required|in:active,inactive,suspended'
    ];

    public function mount()
    {
        $this->loadUserStats();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedRoleFilter()
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter()
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
    
    public function loadUserStats()
    {
        $cacheKey = 'user_stats_' . md5($this->roleFilter . $this->statusFilter);
        
        $this->userStats = Cache::remember($cacheKey, 300, function () {
            $service = app(UserManagementService::class);
            return $service->getUserStatistics();
        });
    }
    
    public function openUserModal($userId = null)
    {
        if ($userId) {
            $user = User::findOrFail($userId);
            $this->userForm = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status
            ];
        } else {
            $this->resetUserForm();
        }
        
        $this->showUserModal = true;
    }
    
    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->resetUserForm();
        $this->resetValidation();
    }
    
    public function saveUser()
    {
        $this->validate();
        
        try {
            $service = app(UserManagementService::class);
            
            if ($this->userForm['id']) {
                $service->updateUser($this->userForm['id'], $this->userForm);
                $message = 'User updated successfully!';
            } else {
                $service->createUser($this->userForm);
                $message = 'User created successfully!';
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);
            
            $this->closeUserModal();
            $this->loadUserStats();
            $this->clearUserCache();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function deleteUser($userId)
    {
        try {
            $service = app(UserManagementService::class);
            $service->deleteUser($userId);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'User deleted successfully!'
            ]);
            
            $this->loadUserStats();
            $this->clearUserCache();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function toggleUserStatus($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            
            $service = app(UserManagementService::class);
            $service->updateUserStatus($userId, $newStatus);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'User status updated successfully!'
            ]);
            
            $this->loadUserStats();
            $this->clearUserCache();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function toggleUserSelection($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }
        
        $this->showBulkActions = count($this->selectedUsers) > 0;
    }
    
    public function selectAllUsers()
    {
        $users = $this->getUsers();
        $this->selectedUsers = $users->pluck('id')->toArray();
        $this->showBulkActions = true;
    }
    
    public function clearSelection()
    {
        $this->selectedUsers = [];
        $this->showBulkActions = false;
    }
    
    public function bulkActivate()
    {
        $this->executeBulkAction('activate');
    }
    
    public function bulkDeactivate()
    {
        $this->executeBulkAction('deactivate');
    }
    
    public function bulkDelete()
    {
        $this->executeBulkAction('delete');
    }
    
    protected function executeBulkAction($action)
    {
        try {
            $service = app(UserManagementService::class);
            $count = $service->bulkAction($this->selectedUsers, $action);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Bulk action completed for {$count} users!"
            ]);
            
            $this->clearSelection();
            $this->loadUserStats();
            $this->clearUserCache();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function exportUsers($format = 'csv')
    {
        try {
            $service = app(UserManagementService::class);
            $fileName = $service->exportUsers($format, [
                'search' => $this->search,
                'role' => $this->roleFilter,
                'status' => $this->statusFilter
            ]);
            
            $this->dispatch('downloadFile', $fileName);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Users exported successfully!'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ]);
        }
    }
    
    protected function getUsers()
    {
        $query = User::query();
        
        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply role filter
        if ($this->roleFilter !== 'all') {
            $query->where('role', $this->roleFilter);
        }
        
        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }
        
        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        return $query->paginate($this->perPage);
    }
    
    protected function resetUserForm()
    {
        $this->userForm = [
            'id' => null,
            'name' => '',
            'email' => '',
            'role' => 'user',
            'status' => 'active'
        ];
    }
    
    protected function clearUserCache()
    {
        Cache::forget('user_stats_' . md5($this->roleFilter . $this->statusFilter));
        Cache::tags(['users'])->flush();
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => $this->getUsers()
        ]);
    }
}
