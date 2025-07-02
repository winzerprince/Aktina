<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserManagementService
{
    public function getUserStatistics()
    {
        return [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'new_this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
            'by_role' => User::groupBy('role')->selectRaw('role, count(*) as count')->pluck('count', 'role')->toArray(),
            'growth_rate' => $this->calculateGrowthRate(),
            'last_login_stats' => $this->getLastLoginStats()
        ];
    }
    
    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'is_active' => $data['is_active'] ?? true,
                'company_name' => $data['company_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make('password123'), // Default password
                'email_verified_at' => now()
            ];

            $user = User::create($userData);
            
            // Log the action
            $this->logUserAction('created', $user->id);
            
            return $user;
        });
    }
    
    public function updateUser($userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $user = User::findOrFail($userId);
            
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'is_active' => $data['is_active'],
                'company_name' => $data['company_name'] ?? null,
                'phone' => $data['phone'] ?? null,
            ];

            $user->update($updateData);
            
            // Log the action
            $this->logUserAction('updated', $user->id, $updateData);
            
            return $user;
        });
    }
    
    public function deleteUser($userId)
    {
        return DB::transaction(function () use ($userId) {
            $user = User::findOrFail($userId);
            
            // Check if user can be deleted (no orders, etc.)
            if ($user->orders()->exists()) {
                throw new \Exception('Cannot delete user with existing orders');
            }
            
            $user->delete();
            
            // Log the action
            $this->logUserAction('deleted', $userId);
            
            return true;
        });
    }
    
    public function toggleUserStatus($userId)
    {
        return DB::transaction(function () use ($userId) {
            $user = User::findOrFail($userId);
            $newStatus = !$user->is_active;
            
            $user->update(['is_active' => $newStatus]);
            
            // Log the action
            $this->logUserAction('status_changed', $userId, [
                'old_status' => !$newStatus,
                'new_status' => $newStatus
            ]);
            
            return $user;
        });
    }
    
    public function bulkAction(array $userIds, $action)
    {
        return $this->executeBulkAction($action, $userIds);
    }
    
    public function exportUsers($format = 'csv', $filters = [])
    {
        $query = User::query();
        
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['role']) && $filters['role'] !== 'all') {
            $query->where('role', $filters['role']);
        }
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        
        $users = $query->get();
        $fileName = 'users_export_' . date('Y-m-d_H-i-s') . '.' . $format;
        $filePath = storage_path('app/exports/' . $fileName);
        
        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        if ($format === 'csv') {
            $this->exportToCsv($users, $filePath);
        } else {
            $this->exportToJson($users, $filePath);
        }
        
        return $fileName;
    }
    
    public function resetUserPassword($userId, $newPassword = null)
    {
        return DB::transaction(function () use ($userId, $newPassword) {
            $user = User::findOrFail($userId);
            
            $password = $newPassword ?: $this->generateRandomPassword();
            
            $user->update([
                'password' => Hash::make($password)
            ]);
            
            // Log the action
            $this->logUserAction('password_reset', $user->id);
            
            return $password;
        });
    }
    
    public function impersonateUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Log the impersonation
        $this->logUserAction('impersonated', $user->id);
        
        return $user;
    }
    
    protected function calculateGrowthRate()
    {
        $currentMonth = User::whereMonth('created_at', Carbon::now()->month)->count();
        $previousMonth = User::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        
        if ($previousMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }
        
        return (($currentMonth - $previousMonth) / $previousMonth) * 100;
    }
    
    protected function getLastLoginStats()
    {
        return [
            'today' => User::whereDate('last_login', today())->count(),
            'this_week' => User::whereBetween('last_login', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'this_month' => User::whereMonth('last_login', Carbon::now()->month)->count(),
            'never_logged_in' => User::whereNull('last_login')->count()
        ];
    }
    
    private function logUserAction($action, $userId, $details = null)
    {
        // Simplified logging - in production, use proper audit logging
        logger()->info("User {$action}", [
            'user_id' => $userId,
            'admin_id' => auth()->id(),
            'details' => $details,
            'timestamp' => now()
        ]);
    }

    public function getFilteredUsers(array $filters, int $perPage = 20)
    {
        $query = User::query();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('company_name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['status'])) {
            $query->where('is_active', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function executeBulkAction(string $action, array $userIds)
    {
        return DB::transaction(function () use ($action, $userIds) {
            $users = User::whereIn('id', $userIds);
            $count = 0;

            switch ($action) {
                case 'activate':
                    $count = $users->update(['is_active' => true]);
                    $message = "{$count} users activated successfully";
                    break;

                case 'deactivate':
                    $count = $users->update(['is_active' => false]);
                    $message = "{$count} users deactivated successfully";
                    break;

                case 'delete':
                    // Soft delete or hard delete based on business rules
                    $count = $users->delete();
                    $message = "{$count} users deleted successfully";
                    break;

                case 'export':
                    $this->exportUsers(['user_ids' => $userIds]);
                    $message = "Export initiated for {$count} users";
                    break;

                case 'send_notification':
                    $this->sendBulkNotification($userIds);
                    $message = "Notifications sent to {$count} users";
                    break;

                default:
                    throw new \InvalidArgumentException('Invalid bulk action');
            }

            // Log bulk action
            $this->logUserAction('bulk_' . $action, null, [
                'user_ids' => $userIds,
                'count' => $count
            ]);

            return ['message' => $message, 'count' => $count];
        });
    }

    private function sendBulkNotification(array $userIds)
    {
        // In a real implementation, this would send notifications
        // For now, we'll just log the action
        $this->logUserAction('bulk_notification_sent', null, ['user_ids' => $userIds]);
        
        return true;
    }
    
    protected function generateRandomPassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle($characters), 0, $length);
    }
    
    protected function exportToCsv($users, $filePath)
    {
        $handle = fopen($filePath, 'w');
        
        // Headers
        fputcsv($handle, [
            'ID',
            'Name',
            'Email',
            'Role',
            'Status',
            'Created At',
            'Last Login',
            'Email Verified'
        ]);
        
        // Data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->status,
                $user->created_at?->format('Y-m-d H:i:s'),
                $user->last_login?->format('Y-m-d H:i:s'),
                $user->email_verified_at ? 'Yes' : 'No'
            ]);
        }
        
        fclose($handle);
    }
    
    protected function exportToJson($users, $filePath)
    {
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'created_at' => $user->created_at?->toISOString(),
                'last_login' => $user->last_login?->toISOString(),
                'email_verified' => $user->email_verified_at !== null
            ];
        });
        
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
