<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product')->insert([
            [
                'name' => 'Premium Widget',
                'sku' => 'PWG-001',
                'description' => 'High-quality premium widget for industrial use',
                'msrp' => 299.99,
                'bom_id' => null, // Will be set after BOM is created
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard Widget',
                'sku' => 'SWG-001',
                'description' => 'Standard widget for general purpose use',
                'msrp' => 199.99,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
