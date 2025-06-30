<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 employees for Aktina
        Employee::factory()
            ->count(20)
            ->create([
                'status' => 'available',
                'current_activity' => 'none',
                'order_id' => null,
                'production_id' => null
            ]);
    }
}
