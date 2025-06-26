<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {        // Create various products
        Product::factory(30)->create();

        // Create some premium products
        Product::factory(10)->premium()->create();

        // Create some budget products
        Product::factory(10)->budget()->create();
    }
}
