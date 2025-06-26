<?php

namespace Database\Seeders;

use App\Models\Bom;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BomSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing products to create BOMs for them
        $products = Product::all();

        // Create BOMs for existing products (one-to-one relationship)
        foreach ($products->take(25) as $product) {
            Bom::factory()->create(['product_id' => $product->id]);
        }

        // Create additional BOMs with new products
        Bom::factory(10)->create();

        // Create some expensive BOMs
        Bom::factory(8)->expensive()->create();

        // Create some budget BOMs
        Bom::factory(5)->budget()->create();
    }
}
