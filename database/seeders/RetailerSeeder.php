<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetailerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('retailer')->insert([
            [
                'user_id' => 4, // Bob Retailer
                'vendor_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
