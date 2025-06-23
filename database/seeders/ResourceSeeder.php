<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('resource')->insert([
            [
                'name' => 'Steel Sheets',
                'units' => 500,
                'reorder_level' => 100,
                'overstock_level' => 1000,
                'description' => 'High-grade steel sheets for manufacturing',
                'supplier_id' => 1,
                'bom_id' => null, // Will be set after BOM is created
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aluminum Bars',
                'units' => 300,
                'reorder_level' => 50,
                'overstock_level' => 800,
                'description' => 'Lightweight aluminum bars',
                'supplier_id' => 1,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plastic Pellets',
                'units' => 1200,
                'reorder_level' => 200,
                'overstock_level' => 2000,
                'description' => 'High-quality plastic pellets for injection molding',
                'supplier_id' => 1,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
