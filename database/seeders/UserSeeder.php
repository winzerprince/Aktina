<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create specific test users with exact credentials as requested

        // 1. Admin user
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'verified' => true,
            'email_verified_at' => now(),
            'company_name' => 'Aktina',
            'address' => json_encode(['street' => '1 Admin Street', 'city' => 'Admin City', 'zip' => '00001']),
        ]);

        // 2. Vendor user
        User::factory()->create([
            'name' => 'vendor',
            'email' => 'vendor@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'verified' => true,
            'email_verified_at' => now(),
            'company_name' => 'Vendor Company',
            'address' => json_encode(['street' => '1 Vendor Street', 'city' => 'Vendor City', 'zip' => '00002']),
        ]);

        // 3. Retailer user
        User::factory()->create([
            'name' => 'retailer',
            'email' => 'retailer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'retailer',
            'verified' => true,
            'email_verified_at' => now(),
            'company_name' => 'Retailer Company',
            'address' => json_encode(['street' => '1 Retailer Street', 'city' => 'Retailer City', 'zip' => '00003']),
        ]);

        // 4. Supplier user
        User::factory()->create([
            'name' => 'supplier',
            'email' => 'supplier@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'verified' => true,
            'email_verified_at' => now(),
            'company_name' => 'Supplier Company',
            'address' => json_encode(['street' => '1 Supplier Street', 'city' => 'Supplier City', 'zip' => '00004']),
        ]);

        // 5. Production Manager user
        User::factory()->create([
            'name' => 'production_manager',
            'email' => 'production_manager@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'production_manager',
            'verified' => true,
            'email_verified_at' => now(),
            'company_name' => 'Aktina',
            'address' => json_encode(['street' => '1 Production Street', 'city' => 'Production City', 'zip' => '00005']),
        ]);

        // 6. HR Manager user
        User::factory()->create([
            'name' => 'hr_manager',
            'email' => 'hr_manager@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'hr_manager',
            'verified' => true,
            'email_verified_at' => now(),
            'company_name' => 'Aktina',
            'address' => json_encode(['street' => '1 HR Street', 'city' => 'HR City', 'zip' => '00006']),
        ]);

        // Create additional random users for different roles using snake_case
        User::factory(10)->create(['role' => 'admin']);
        User::factory(15)->create(['role' => 'supplier']);
        User::factory(20)->create(['role' => 'vendor']);
        User::factory(25)->create(['role' => 'retailer']);
        User::factory(12)->create(['role' => 'production_manager']);
        User::factory(8)->create(['role' => 'hr_manager']);
    }
}
