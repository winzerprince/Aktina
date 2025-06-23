<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rating')->insert([
            [
                'rating' => 5,
                'product_id' => 1, // Premium Widget
                'retailer_id' => 1, // Bob Retailer
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rating' => 4,
                'product_id' => 2, // Standard Widget
                'retailer_id' => 1, // Bob Retailer
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
