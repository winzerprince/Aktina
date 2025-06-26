<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with supplier role to link to suppliers
        $supplierUsers = User::where('role', 'supplier')->get();

        // Create suppliers linked to existing users
        foreach ($supplierUsers->take(10) as $user) {
            Supplier::factory()->create(['user_id' => $user->id]);
        }

        // Create additional suppliers with new users
        Supplier::factory(15)->create();

        // Create some preferred suppliers
        Supplier::factory(5)->preferred()->create();
    }
}
