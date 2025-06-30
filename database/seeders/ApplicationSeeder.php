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
                    // Create some applications with processed PDFs
                    if (rand(1, 10) <= 6) { // 60% chance of being processed
                        Application::factory()->processed()->create(['vendor_id' => $vendor->id]);
                    } else {
                        Application::factory()->unprocessed()->create(['vendor_id' => $vendor->id]);
                    }
                }
            }
        }

        // Create additional applications - each needs a vendor
        // Since each vendor can have only one application (unique constraint),
        // we need to create new vendors for additional applications
        for ($i = 0; $i < 10; $i++) {
            $vendor = Vendor::factory()->create();
            Application::factory()->create([
                'vendor_id' => $vendor->id,
                'application_reference' => 'APP-' . str_pad($i + 100, 6, '0', STR_PAD_LEFT),
                'pdf_path' => 'storage/applications/app_' . uniqid() . '.pdf',
            ]);
        }

        // Create some approved applications with new vendors and PDF processing
        for ($i = 0; $i < 5; $i++) {
            $vendor = Vendor::factory()->create();
            Application::factory()->approved()->processed()->create([
                'vendor_id' => $vendor->id,
                'application_reference' => 'APP-' . str_pad($i + 200, 6, '0', STR_PAD_LEFT),
                'pdf_path' => 'storage/applications/approved_' . uniqid() . '.pdf',
                'processing_notes' => 'Automatically approved based on criteria. Java processing complete.',
            ]);
        }

        // Create some pending applications with new vendors awaiting processing
        for ($i = 0; $i < 8; $i++) {
            $vendor = Vendor::factory()->create();
            Application::factory()->pending()->unprocessed()->create([
                'vendor_id' => $vendor->id,
                'application_reference' => 'APP-' . str_pad($i + 300, 6, '0', STR_PAD_LEFT),
                'pdf_path' => 'storage/applications/pending_' . uniqid() . '.pdf',
            ]);
        }
    }
}
