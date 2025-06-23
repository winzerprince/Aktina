<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('production')->insert([
            [
                'units' => 10000,
                'status' => 'in_progress',
                'completed_units' => 3500,
                'in_progress_units' => 4000,
                'cancelled_units' => 0,
                'assembly_line' => 'Flagship Line A',
                'product_id' => 1, // Aktina Pro 15
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'units' => 25000,
                'status' => 'completed',
                'completed_units' => 25000,
                'in_progress_units' => 0,
                'cancelled_units' => 0,
                'assembly_line' => 'Mid-Range Line B',
                'product_id' => 2, // Aktina Lite 12
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'units' => 50000,
                'status' => 'pending',
                'completed_units' => 0,
                'in_progress_units' => 0,
                'cancelled_units' => 0,
                'assembly_line' => 'Budget Line C',
                'product_id' => 3, // Aktina Essential 10
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
