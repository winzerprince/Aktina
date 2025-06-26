<?php

namespace Database\Seeders;

use App\Models\RetailerListing;
use App\Models\Application;
use Illuminate\Database\Seeder;

class RetailerListingSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing applications to link retailer listings
        $applications = Application::all();

        // Create retailer listings for applications
        if ($applications->count() > 0) {
            foreach ($applications->take(12) as $application) {
                if (rand(1, 10) <= 6) { // 60% chance of having a retailer listing
                    RetailerListing::factory()->forApplication($application->id)->create();
                }
            }
        }

        // Create additional retailer listings - each needs an application
        // Each application needs a vendor due to foreign key constraint
        for ($i = 0; $i < 15; $i++) {
            $vendor = \App\Models\Vendor::factory()->create();
            $application = Application::factory()->create(['vendor_id' => $vendor->id]);
            RetailerListing::factory()->forApplication($application->id)->create();
        }
    }
}
