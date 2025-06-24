<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionManagerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('production_manager')->insert([
            [
                'user_id' => 6, // Production Manager
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
