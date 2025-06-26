<?php

namespace Database\Seeders;

use App\Models\Retailer;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class RetailerSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with retailer role and existing vendors
        $retailerUsers = User::where('role', 'retailer')->get();
        $vendors = Vendor::all();

        // Create retailers linked to existing users and vendors
        if ($vendors->count() > 0) {
            foreach ($retailerUsers->take(10) as $user) {
                Retailer::factory()->forUserAndVendor($user->id, $vendors->random()->id)->create();
            }
        }

        // Create additional retailers with new users and vendors
        Retailer::factory(20)->create();

        // Create some verified retailers
        Retailer::factory(8)->verified()->create();

        // Create some large business retailers
        Retailer::factory(5)->largeBusiness()->create();
    }
}
