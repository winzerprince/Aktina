<?php

namespace Database\Seeders;

use App\Models\RetailerListing;
use App\Models\Application;
use Illuminate\Database\Seeder;

class RetailerListingSeeder extends Seeder
{
    public function run(): void
    {
        // First, create a retailer listing for the test retailer connecting to the test vendor
        $testVendorUser = \App\Models\User::where('email', 'vendor@gmail.com')->first();
        $testRetailerUser = \App\Models\User::where('email', 'retailer@gmail.com')->first();
        
        if ($testVendorUser && $testRetailerUser) {
            $testVendor = \App\Models\Vendor::where('user_id', $testVendorUser->id)->first();
            $testApplication = \App\Models\Application::where('vendor_id', $testVendor?->id)->first();
            
            if ($testApplication) {
                \App\Models\RetailerListing::factory()->create([
                    'application_id' => $testApplication->id,
                    'retailer_email' => $testRetailerUser->email,
                ]);
            }
        }

        // Get existing applications to link retailer listings
        $applications = Application::all();

        // Create retailer listings for applications
        if ($applications->count() > 0) {
            foreach ($applications->take(12) as $application) {
                // Skip the test application as it already has a listing
                if ($application->application_reference === 'APP-TEST-001') {
                    continue;
                }
                
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
