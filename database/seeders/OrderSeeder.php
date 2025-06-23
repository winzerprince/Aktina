<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order')->insert([
            [
                'price' => 599.98,
                'items' => json_encode([
                    ['product_id' => 1, 'quantity' => 1, 'price' => 299.99],
                    ['product_id' => 2, 'quantity' => 1, 'price' => 199.99],
                ]),
                'buyer_id' => 4, // Bob Retailer
                'seller_id' => 3, // Jane Vendor
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
