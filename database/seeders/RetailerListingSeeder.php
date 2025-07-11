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
                // Check if listing already exists to avoid duplicates
                $existingListing = \App\Models\RetailerListing::where('retailer_email', $testRetailerUser->email)
                    ->where('application_id', $testApplication->id)
                    ->first();

                if (!$existingListing) {
                    \App\Models\RetailerListing::factory()
                        ->forApplication($testApplication->id)
                        ->forRetailer($testRetailerUser)
                        ->create();
                } else {
                    // Update existing listing with retailer_id if missing
                    if (!$existingListing->retailer_id) {
                        $existingListing->update(['retailer_id' => $testRetailerUser->id]);
                    }
                }
            }
        }

        // Get all retailers and vendors for connections
        $retailers = \App\Models\User::where('role', 'retailer')->get();
        $vendors = \App\Models\Vendor::with('user')->get();

        // Create connections between vendors and retailers
        foreach ($vendors->take(5) as $vendor) {
            // Skip test vendor as it already has connections
            if ($vendor->user && $vendor->user->email === 'vendor@gmail.com') {
                continue;
            }

            // Create 2-4 retailer connections per vendor
            $connectionCount = rand(2, 4);
            $selectedRetailers = $retailers->random(min($connectionCount, $retailers->count()));

            foreach ($selectedRetailers as $retailer) {
                // Check if connection already exists
                $existingConnection = \App\Models\RetailerListing::where('retailer_email', $retailer->email)
                    ->whereHas('application', function($query) use ($vendor) {
                        $query->where('vendor_id', $vendor->id);
                    })->exists();

                if (!$existingConnection) {
                    // Create application for this vendor-retailer connection
                    $application = \App\Models\Application::factory()->create([
                        'vendor_id' => $vendor->id,
                        'status' => collect(['pending', 'approved', 'rejected'])->random(),
                    ]);

                    // Create retailer listing with proper retailer_id
                    \App\Models\RetailerListing::factory()
                        ->forApplication($application->id)
                        ->forRetailer($retailer)
                        ->create();
                }
            }
        }

        // Create some additional standalone applications and listings
        for ($i = 0; $i < 10; $i++) {
            $vendor = \App\Models\Vendor::factory()->create();
            $retailer = $retailers->random();

            $application = \App\Models\Application::factory()->create([
                'vendor_id' => $vendor->id,
                'status' => collect(['pending', 'approved', 'rejected'])->random(),
            ]);

            \App\Models\RetailerListing::factory()
                ->forApplication($application->id)
                ->forRetailer($retailer)
                ->create();
        }
    }
}
