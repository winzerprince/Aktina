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
                'name' => 'Sarah Chen',
                'email' => 'admin@aktina.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'verified' => true,
                'company_name' => 'Aktina Technologies',
                'address' => json_encode(['street' => '1 Infinite Loop', 'city' => 'Cupertino', 'zip' => '95014']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mike Rodriguez',
                'email' => 'supplier@qualcomm.com',
                'password' => Hash::make('password'),
                'role' => 'supplier',
                'verified' => true,
                'company_name' => 'Qualcomm Technologies',
                'address' => json_encode(['street' => '5775 Morehouse Dr', 'city' => 'San Diego', 'zip' => '92121']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lisa Wang',
                'email' => 'vendor@techparts.com',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'verified' => true,
                'company_name' => 'TechParts Distribution',
                'address' => json_encode(['street' => '123 Electronics Blvd', 'city' => 'Shenzhen', 'zip' => '518000']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'David Kim',
                'email' => 'retailer@mobilezone.com',
                'password' => Hash::make('password'),
                'role' => 'retailer',
                'verified' => true,
                'company_name' => 'MobileZone Retail',
                'address' => json_encode(['street' => '456 Commerce St', 'city' => 'New York', 'zip' => '10001']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jennifer Park',
                'email' => 'hr@aktina.com',
                'password' => Hash::make('password'),
                'role' => 'hr_manager',
                'verified' => true,
                'company_name' => 'Aktina Technologies',
                'address' => json_encode(['street' => '1 Infinite Loop', 'city' => 'Cupertino', 'zip' => '95014']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Robert Zhang',
                'email' => 'production@aktina.com',
                'password' => Hash::make('password'),
                'role' => 'production_manager',
                'verified' => true,
                'company_name' => 'Aktina Technologies',
                'address' => json_encode(['street' => '100 Manufacturing Way', 'city' => 'Austin', 'zip' => '78701']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
