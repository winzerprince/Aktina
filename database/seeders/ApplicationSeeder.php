<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing vendors to assign applications to some of them
        $vendors = Vendor::all();

        if ($vendors->count() > 0) {
            // Create applications for some vendors (not all vendors need applications)
            foreach ($vendors->take(15) as $vendor) {
                if (rand(1, 10) <= 7) { // 70% chance of having an application
                    Application::factory()->create(['vendor_id' => $vendor->id]);
                }
            }
        }

        // Create additional applications - each needs a vendor
        // Since each vendor can have only one application (unique constraint),
        // we need to create new vendors for additional applications
        for ($i = 0; $i < 10; $i++) {
            $vendor = Vendor::factory()->create();
            Application::factory()->create(['vendor_id' => $vendor->id]);
        }

        // Create some approved applications with new vendors
        for ($i = 0; $i < 5; $i++) {
            $vendor = Vendor::factory()->create();
            Application::factory()->approved()->create(['vendor_id' => $vendor->id]);
        }

        // Create some pending applications with new vendors
        for ($i = 0; $i < 8; $i++) {
            $vendor = Vendor::factory()->create();
            Application::factory()->pending()->create(['vendor_id' => $vendor->id]);
        }
    }
}
