<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrManagerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hr_manager')->insert([
            [
                'user_id' => 5, // HR Manager
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
