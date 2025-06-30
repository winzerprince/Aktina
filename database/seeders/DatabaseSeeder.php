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
            // 1. First create users (no dependencies)
            UserSeeder::class,

            // 2. Create user-type tables that depend on users
            SupplierSeeder::class,
            AdminSeeder::class,
            HrManagerSeeder::class,
            ProductionManagerSeeder::class,

            // 3. Create vendors (depend on users, may have applications later)
            VendorSeeder::class,

            // 4. Create applications (depend on vendors)
            ApplicationSeeder::class,

            // 5. Create retailers (depend on users and vendors)
            RetailerSeeder::class,

            // 6. Create retailer listings (depend on applications)
            RetailerListingSeeder::class,

            // 7. Create products (independent)
            ProductSeeder::class,

            // 8. Create BOMs (depend on products)
            BomSeeder::class,

            // 9. Create resources (depend on suppliers and BOMs)
            ResourceSeeder::class,

            // 10. Create production (depends on products)
            ProductionSeeder::class,

            // 11. Create ratings (depend on products and retailers)
            RatingSeeder::class,

            // 12. Create reports
            ReportSeeder::class,

            // 13. Create employees (standalone table)
            EmployeeSeeder::class,

            // 14. Create orders (depend on users as buyers/sellers)
            OrderSeeder::class,

            // 15. Create resource orders (depend on suppliers and resources)
            ResourceOrderSeeder::class,
        ]);
    }
}
