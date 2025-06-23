<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetailerListingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('retailer_listing')->insert([
            [
                'retailer_email' => 'retailer@example.com',
                'application_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Update vendor with retailer_listing_id
        DB::table('vendor')->where('id', 1)->update([
            'retailer_listing_id' => 1,
        ]);
    }
}
