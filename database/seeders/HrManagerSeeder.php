<?php

namespace Database\Seeders;

use App\Models\HrManager;
use App\Models\User;
use Illuminate\Database\Seeder;

class HrManagerSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with hr_manager role to link to HR managers
        $hrUsers = User::where('role', 'hr_manager')->get();        // Create HR managers linked to existing users
        foreach ($hrUsers->take(5) as $user) {
            HrManager::factory()->forUser($user->id)->create();
        }

        // Create additional HR managers with new users
        HrManager::factory(5)->create();
    }
}
