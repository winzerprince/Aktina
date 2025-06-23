<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('application')->insert([
            [
                'status' => 'approved',
                'meeting_schedule' => now()->addDays(7)->toDateString(),
                'vendor_id' => null, // Will be updated after vendor is created
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'pending',
                'meeting_schedule' => null,
                'vendor_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
