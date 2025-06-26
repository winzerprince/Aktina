<?php

namespace Database\Seeders;

use App\Models\ProductionManager;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionManagerSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with production_manager role to link to production managers
        $productionUsers = User::where('role', 'production_manager')->get();        // Create production managers linked to existing users
        foreach ($productionUsers->take(8) as $user) {
            ProductionManager::factory()->forUser($user->id)->create();
        }

        // Create additional production managers with new users
        ProductionManager::factory(5)->create();
    }
}
