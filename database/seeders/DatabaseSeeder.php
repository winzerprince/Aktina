<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SupplierSeeder::class,
            AdminSeeder::class,
            HrManagerSeeder::class,
            ProductionManagerSeeder::class,
            ApplicationSeeder::class, // Move before vendor
            VendorSeeder::class,
            RetailerSeeder::class,
            RetailerListingSeeder::class,
            ResourceSeeder::class,
            ProductSeeder::class,
            BomSeeder::class,
            ProductionSeeder::class,
            RatingSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
