<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\Product;
use App\Models\Retailer;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing products and retailers
        $products = Product::all();
        $retailers = Retailer::all();

        // Create ratings for existing products and retailers
        if ($products->count() > 0 && $retailers->count() > 0) {
            foreach ($products->take(25) as $product) {
                // Each product gets 2-8 ratings
                $ratingCount = rand(2, 8);
                for ($i = 0; $i < $ratingCount; $i++) {
                    Rating::factory()->create([
                        'product_id' => $product->id,
                        'retailer_id' => $retailers->random()->id,
                    ]);
                }
            }
        }

        // Create additional ratings with new products and retailers
        Rating::factory(50)->create();

        // Create some excellent ratings
        Rating::factory(15)->excellent()->create();

        // Create some poor ratings
        Rating::factory(8)->poor()->create();
    }
}
