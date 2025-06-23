<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bom')->insert([
            [
                'price' => 150.00,
                'product_id' => 1, // Premium Widget
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'price' => 100.00,
                'product_id' => 2, // Standard Widget
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Update products with BOM IDs
        DB::table('product')->where('id', 1)->update(['bom_id' => 1]);
        DB::table('product')->where('id', 2)->update(['bom_id' => 2]);

        // Update resources with BOM IDs
        DB::table('resource')->where('id', 1)->update(['bom_id' => 1]);
        DB::table('resource')->where('id', 2)->update(['bom_id' => 1]);
        DB::table('resource')->where('id', 3)->update(['bom_id' => 2]);
    }
}
