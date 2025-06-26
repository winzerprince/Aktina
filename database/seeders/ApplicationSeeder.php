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

        // Create additional applications with new vendors
        Application::factory(10)->create();        // Create some approved applications with new vendors
        Application::factory(5)->approved()->create();

        // Create some pending applications with new vendors
        Application::factory(8)->pending()->create();
    }
}
