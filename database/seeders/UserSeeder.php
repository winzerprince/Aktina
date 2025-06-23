<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'verified' => true,
                'company_name' => 'Aktina Corp',
                'address' => json_encode(['street' => '123 Admin St', 'city' => 'Admin City', 'zip' => '12345']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'John Supplier',
                'email' => 'supplier@example.com',
                'password' => Hash::make('password'),
                'role' => 'supplier',
                'verified' => true,
                'company_name' => 'Supply Co',
                'address' => json_encode(['street' => '456 Supply Ave', 'city' => 'Supply City', 'zip' => '23456']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Vendor',
                'email' => 'vendor@example.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'verified' => true,
                'company_name' => 'Vendor Inc',
                'address' => json_encode(['street' => '789 Vendor Blvd', 'city' => 'Vendor City', 'zip' => '34567']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bob Retailer',
                'email' => 'retailer@example.com',
                'password' => Hash::make('password'),
                'role' => 'retailer',
                'verified' => true,
                'company_name' => 'Retail Store',
                'address' => json_encode(['street' => '321 Retail St', 'city' => 'Retail City', 'zip' => '45678']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'password' => Hash::make('password'),
                'role' => 'hr_manager',
                'verified' => true,
                'company_name' => 'Aktina Corp',
                'address' => json_encode(['street' => '123 HR St', 'city' => 'HR City', 'zip' => '56789']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Production Manager',
                'email' => 'production@example.com',
                'password' => Hash::make('password'),
                'role' => 'production_manager',
                'verified' => true,
                'company_name' => 'Aktina Corp',
                'address' => json_encode(['street' => '123 Production St', 'city' => 'Production City', 'zip' => '67890']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
