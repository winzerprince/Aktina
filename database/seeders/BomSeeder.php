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
                'price' => 420.00, // Total component cost for flagship
                'product_id' => 1, // Aktina Pro 15
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'price' => 180.00, // Total component cost for mid-range
                'product_id' => 2, // Aktina Lite 12
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'price' => 85.00, // Total component cost for budget
                'product_id' => 3, // Aktina Essential 10
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Update products with BOM IDs
        DB::table('product')->where('id', 1)->update(['bom_id' => 1]);
        DB::table('product')->where('id', 2)->update(['bom_id' => 2]);
        DB::table('product')->where('id', 3)->update(['bom_id' => 3]);

        // Update resources with BOM IDs (flagship phone components)
        DB::table('resource')->where('id', 1)->update(['bom_id' => 1]); // Snapdragon 8 Gen 3
        DB::table('resource')->where('id', 3)->update(['bom_id' => 1]); // AMOLED Display
        DB::table('resource')->where('id', 4)->update(['bom_id' => 1]); // Sony Camera
        DB::table('resource')->where('id', 5)->update(['bom_id' => 1]); // 5000mAh Battery
        DB::table('resource')->where('id', 6)->update(['bom_id' => 1]); // 12GB RAM

        // Mid-range phone components
        DB::table('resource')->where('id', 2)->update(['bom_id' => 2]); // MediaTek Dimensity
    }
}
