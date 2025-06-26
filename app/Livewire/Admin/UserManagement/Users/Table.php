<?php

namespace App\Livewire\Admin\UserManagement\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public function toggleVerification($userId)
    {
        $user = User::findOrFail($userId);

        // Toggle email verification status
        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            $status = 'email unverified';
        } else {
            $user->email_verified_at = now();
            $status = 'email verified';
        }

        $user->save();

        // Add a success message
        session()->flash('message', "User {$user->name} has been {$status}.");
    }

    public function render()
    {
        $users = User::query()
            ->with(['admin', 'supplier', 'vendor', 'retailer', 'hrManager', 'productionManager'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.user-management.users.table', [
            'users' => $users
        ]);
    }
}
