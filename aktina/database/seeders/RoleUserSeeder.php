<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for each role
        $roles = [
            'supplier' => 'John Supplier',
            'production_manager' => 'Jane Production',
            'hr_manager' => 'Mike HR Manager',
            'system_administrator' => 'Sarah Admin',
            'wholesaler' => 'Bob Wholesaler',
            'retailer' => 'Alice Retailer',
        ];

        foreach ($roles as $role => $name) {
            User::factory()->create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@aktina.test',
                'role' => $role,
            ]);
        }

        // Create additional test users
        User::factory(5)->supplier()->create();
        User::factory(3)->productionManager()->create();
        User::factory(2)->hrManager()->create();
        User::factory(1)->systemAdministrator()->create();
        User::factory(4)->wholesaler()->create();
        User::factory(10)->retailer()->create();
    }
}
