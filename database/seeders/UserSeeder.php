<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create specific admin user
        User::factory()->create([
            'name' => 'Sarah Chen',
            'email' => 'admin@aktina.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'verified' => true,
            'company_name' => 'Aktina Technologies',
            'address' => json_encode(['street' => '1 Infinite Loop', 'city' => 'Cupertino', 'zip' => '95014']),
        ]);

        // Create specific supplier user
        User::factory()->create([
            'name' => 'Mike Rodriguez',
            'email' => 'supplier@qualcomm.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'verified' => true,
            'company_name' => 'Qualcomm Inc.',
            'address' => json_encode(['street' => '5775 Morehouse Dr', 'city' => 'San Diego', 'zip' => '92121']),
        ]);

        // Create specific vendor user
        User::factory()->create([
            'name' => 'Emily Watson',
            'email' => 'vendor@technovation.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'verified' => true,
            'company_name' => 'Technovation Ltd.',
            'address' => json_encode(['street' => '456 Innovation Blvd', 'city' => 'Austin', 'zip' => '78701']),
        ]);

        // Create specific retailer user
        User::factory()->create([
            'name' => 'David Kim',
            'email' => 'retailer@mobilezone.com',
            'password' => Hash::make('password'),
            'role' => 'retailer',
            'verified' => true,
            'company_name' => 'MobileZone Retail',
            'address' => json_encode(['street' => '456 Commerce St', 'city' => 'New York', 'zip' => '10001']),
        ]);

        // Create additional random users for different roles
        User::factory(10)->create(['role' => 'admin']);
        User::factory(15)->create(['role' => 'supplier']);
        User::factory(20)->create(['role' => 'vendor']);
        User::factory(25)->create(['role' => 'retailer']);
        User::factory(12)->create(['role' => 'production_manager']);
        User::factory(8)->create(['role' => 'hr_manager']);
    }
}
