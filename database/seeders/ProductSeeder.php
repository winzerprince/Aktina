<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get users that can own products
        $potentialOwners = User::whereIn('role', ['admin', 'supplier', 'vendor', 'retailer'])->get();

        if ($potentialOwners->isEmpty()) {
            // Create owners if none exist
            $potentialOwners = collect([
                User::factory()->create(['role' => 'retailer']),
                User::factory()->create(['role' => 'supplier']),
                User::factory()->create(['role' => 'vendor']),
            ]);
        }

        // Create various products with assigned owners
        foreach ($potentialOwners->take(5) as $owner) {
            Product::factory(6)->ownedBy($owner->id)->create();
        }

        // Create some premium products with assigned owners
        foreach ($potentialOwners->take(3) as $owner) {
            Product::factory(3)->premium()->ownedBy($owner->id)->create();
        }

        // Create some budget products with assigned owners
        foreach ($potentialOwners->take(3) as $owner) {
            Product::factory(3)->budget()->ownedBy($owner->id)->create();
        }

        // Create additional products with random owners
        for ($i = 0; $i < 10; $i++) {
            Product::factory()->ownedBy($potentialOwners->random()->id)->create();
        }
    }
}
