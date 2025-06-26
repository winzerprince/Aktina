<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with vendor role to link to vendors
        $vendorUsers = User::where('role', 'vendor')->get();

        // Create vendors linked to existing users
        foreach ($vendorUsers->take(10) as $user) {
            Vendor::factory()->create(['user_id' => $user->id]);
        }

        // Create additional vendors with new users
        Vendor::factory(15)->create();

        // Create some vendors with applications (note: applications will be created separately)
        // We'll update this relationship in ApplicationSeeder
    }
}
