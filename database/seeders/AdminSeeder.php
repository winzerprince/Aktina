<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with admin role to link to admins
        $adminUsers = User::where('role', 'admin')->get();
        
        // Create admins linked to existing users
        foreach ($adminUsers as $user) {
            Admin::factory()->forUser($user->id)->create();
        }

        // Create additional admins with new users
        Admin::factory(5)->create();
    }
}
