<?php

namespace Database\Seeders;

use App\Models\Production;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing products to create production for them
        $products = Product::all();

        // Create production runs for existing products
        if ($products->count() > 0) {
            foreach ($products->take(20) as $product) {
                // Some products may have multiple production runs
                $runs = rand(1, 3);
                for ($i = 0; $i < $runs; $i++) {
                    Production::factory()->create(['product_id' => $product->id]);
                }
            }
        }

        // Create additional production runs with new products
        Production::factory(25)->create();

        // Create some completed production runs
        Production::factory(15)->completed()->create();

        // Create some in-progress production runs
        Production::factory(10)->inProgress()->create();
    }
}
