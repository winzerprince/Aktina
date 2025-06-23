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
                'units' => 100,
                'status' => 'in_progress',
                'completed_units' => 30,
                'in_progress_units' => 50,
                'cancelled_units' => 0,
                'assembly_line' => 'Line 1',
                'product_id' => 1, // Premium Widget
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'units' => 200,
                'status' => 'completed',
                'completed_units' => 200,
                'in_progress_units' => 0,
                'cancelled_units' => 0,
                'assembly_line' => 'Line 2',
                'product_id' => 2, // Standard Widget
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
