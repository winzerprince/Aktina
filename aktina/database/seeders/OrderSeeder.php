<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all suppliers and production managers
        $suppliers = User::where('role', 'supplier')->get();
        $productionManagers = User::where('role', 'production_manager')->get();

        if ($suppliers->isEmpty() || $productionManagers->isEmpty()) {
            $this->command->warn('No suppliers or production managers found. Please run RoleUserSeeder first.');
            return;
        }

        // Create orders for each supplier
        foreach ($suppliers as $supplier) {
            Order::factory(rand(3, 8))->create([
                'supplier_id' => $supplier->id,
                'production_manager_id' => $productionManagers->random()->id,
            ]);
        }
    }
}
