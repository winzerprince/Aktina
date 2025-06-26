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

        // Create BOMs for products
        foreach ($products->take(25) as $product) {
            $bom = Bom::factory()->create(['product_id' => $product->id]);

            // Update product with bom_id
            $product->update(['bom_id' => $bom->id]);
        }        // Create additional BOMs with new products
        Bom::factory(10)->create();

        // Create some expensive BOMs
        Bom::factory(8)->expensive()->create();

        // Create some budget BOMs
        Bom::factory(5)->budget()->create();
    }
}
