<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vendor')->insert([
            [
                'user_id' => 3, // Jane Vendor
                'application_id' => 1,
                'retailer_listing_id' => null, // Will be set after retailer_listing is created
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
