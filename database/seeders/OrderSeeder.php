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
                'price' => 24999.75,
                'items' => json_encode([
                    ['product_id' => 1, 'product_name' => 'Aktina Pro 15', 'quantity' => 25, 'unit_price' => 999.99],
                ]),
                'buyer_id' => 4, // David Kim (Retailer)
                'seller_id' => 3, // Lisa Wang (Vendor)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'price' => 22499.50,
                'items' => json_encode([
                    ['product_id' => 2, 'product_name' => 'Aktina Lite 12', 'quantity' => 50, 'unit_price' => 449.99],
                ]),
                'buyer_id' => 4, // David Kim (Retailer)
                'seller_id' => 3, // Lisa Wang (Vendor)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'price' => 19999.20,
                'items' => json_encode([
                    ['product_id' => 3, 'product_name' => 'Aktina Essential 10', 'quantity' => 100, 'unit_price' => 199.99],
                ]),
                'buyer_id' => 4, // David Kim (Retailer)
                'seller_id' => 3, // Lisa Wang (Vendor)
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
