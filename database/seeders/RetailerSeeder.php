<?php

namespace Database\Seeders;

use App\Models\Retailer;
use App\Models\User;
use Illuminate\Database\Seeder;

class RetailerSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with retailer role
        $retailerUsers = User::where('role', 'retailer')->get();

        // Create retailers linked to existing users
        foreach ($retailerUsers->take(10) as $user) {
            Retailer::factory()->forUser($user->id)->create();
        }

        // Create additional retailers with new users
        Retailer::factory(20)->create();

        // Create some verified retailers
        Retailer::factory(8)->verified()->create();

        // Create some large business retailers
        Retailer::factory(5)->largeBusiness()->create();
    }
}
